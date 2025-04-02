<?php

declare(strict_types=1);

/**************************************************************************************
 *
 * Catalyst PHP Framework
 * PHP Version 8.3 (Required).
 *
 * @package   Catalyst
 * @subpackage Assets
 * @see       https://github.com/arcanisgk/catalyst
 *
 * @author    Walter Nuñez (arcanisgk/original founder) <icarosnet@gmail.com>
 * @copyright 2023 - 2025
 * @license   http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 *
 * @note      This program is distributed in the hope that it will be useful
 *            WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
 *            or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @category  Framework
 * @filesource
 *
 * @link      https://catalyst.dock Local development URL
 *
 * DkimGenerator component for the Catalyst Framework
 *
 */

namespace Catalyst\Framework\Core\Mail;

use Catalyst\Framework\Traits\SingletonTrait;
use Exception;

/**************************************************************************************
 * DkimGenerator - DKIM key generator for email authentication
 *
 * This class provides functionality to generate DomainKeys Identified Mail (DKIM) keys
 * for email authentication systems. DKIM allows email senders to cryptographically sign
 * messages, which receivers can verify to confirm the authenticity of the sender.
 *
 * The class implements the Singleton pattern through the SingletonTrait to ensure
 * only one instance of the key generator exists throughout the application lifecycle.
 *
 * Key features:
 * - Generates RSA key pairs (private/public) for DKIM authentication
 * - Creates required directory structures with appropriate permissions
 * - Saves keys to the filesystem in a structured manner
 * - Generates properly formatted DNS TXT records for domain verification
 * - Organizes keys by domain, selector, and connection ID
 *
 * @package Catalyst\Framework\Core\Mail
 */
class DkimGenerator
{

    use SingletonTrait;

    /**
     * Generate DKIM keys for a domain
     *
     * @param string $domain Domain name
     * @param string $selector Selector name
     * @param string $connectionId Connection identifier
     * @return array Result with keys and DNS record
     * @throws Exception If key generation fails
     */
    public function generateKeys(string $domain, string $selector, string $connectionId): array
    {
        // Create connection-specific directory structure
        $keyDirectory = implode(DS, [PD, 'bootstrap', 'dkim', $domain, $connectionId]);

        // Create directory if it doesn't exist
        if (!is_dir($keyDirectory) && !mkdir($keyDirectory, 0755, true)) {
            throw new Exception("Unable to create directory for DKIM keys: $keyDirectory");
        }

        // Create filenames that include the selector
        $privateKeyPath = $keyDirectory . DS . $selector . '_private.key';
        $publicKeyPath = $keyDirectory . DS . $selector . '_public.key';

        // Generate private key
        $privateKey = openssl_pkey_new([
            'digest_alg' => 'sha256',
            'private_key_bits' => 2048,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
        ]);

        if (!$privateKey) {
            throw new Exception("Failed to generate private key: " . openssl_error_string());
        }

        // Export private key to PEM format
        openssl_pkey_export($privateKey, $privatePem);

        // Save private key
        if (file_put_contents($privateKeyPath, $privatePem) === false) {
            throw new Exception("Failed to save private key to $privateKeyPath");
        }

        // Get public key details
        $keyDetails = openssl_pkey_get_details($privateKey);
        $publicKey = $keyDetails['key'];

        // Save public key
        if (file_put_contents($publicKeyPath, $publicKey) === false) {
            throw new Exception("Failed to save public key to $publicKeyPath");
        }

        // Generate DNS TXT record
        $dnsRecord = $this->generateDnsTxtRecord($domain, $selector, $publicKey);

        // Modifica el return para incluir el dominio
        return [
            'selector' => $selector,
            'privateKeyPath' => $privateKeyPath,
            'publicKeyPath' => $publicKeyPath,
            'dnsRecord' => $dnsRecord,
            'keyPath' => $keyDirectory,
            'domain' => $domain
        ];
    }

    /**
     * Generate DNS TXT record for DKIM
     *
     * @param string $domain Domain name
     * @param string $selector Selector name
     * @param string $publicKey Public key in PEM format
     * @return string DNS TXT record
     */
    private function generateDnsTxtRecord(string $domain, string $selector, string $publicKey): string
    {
        // Extract the base64 encoded part of the public key
        preg_match('/-----BEGIN PUBLIC KEY-----(.*)-----END PUBLIC KEY-----/s', $publicKey, $matches);
        $publicKeyBase64 = trim(str_replace(["\r", "\n"], '', $matches[1]));

        // Format the DNS record
        return sprintf(
            '%s._domainkey.%s TXT "v=DKIM1; k=rsa; p=%s"',
            $selector,
            $domain,
            $publicKeyBase64
        );
    }
}