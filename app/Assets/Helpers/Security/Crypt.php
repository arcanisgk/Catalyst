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
 * Crypt component for the Catalyst Framework
 *
 */

namespace Catalyst\Helpers\Security;

use Catalyst\Framework\Traits\SingletonTrait;

class Crypt
{

    use SingletonTrait;

    /**
     * Encrypt a password for storage
     *
     * @param string $password The password to encrypt
     * @return string The encrypted password with prefix
     */
    public static function encryptPassword(string $password): string
    {
        if (empty($password)) {
            return '';
        }

        // Si ya comienza con "enc:", asumimos que ya está cifrada
        if (str_starts_with($password, 'enc:')) {
            return $password;
        }

        // Usar una clave de cifrado basada en alguna constante única del sistema
        $encryptionKey = defined('CATALYST_KEY') ? CATALYST_KEY : 'default-encryption-key';

        // Cifrar usando openssl (método AES-256-CBC)
        $ivlen = openssl_cipher_iv_length('AES-256-CBC');
        $iv = openssl_random_pseudo_bytes($ivlen);
        $encrypted = openssl_encrypt($password, 'AES-256-CBC', $encryptionKey, 0, $iv);
        $encryptedData = base64_encode($iv . $encrypted);

        // Devolver con prefijo para identificar que está cifrada
        return 'enc:' . $encryptedData;
    }

    /**
     * Decrypt a stored password
     *
     * @param string $encryptedPassword The encrypted password with prefix
     * @return string The decrypted password
     */
    public static function decryptPassword(string $encryptedPassword): string
    {
        // Si la contraseña está vacía, devolverla tal cual
        if (empty($encryptedPassword)) {
            return '';
        }

        // Verificar si la contraseña está cifrada (debe empezar con "enc:")
        if (!str_starts_with($encryptedPassword, 'enc:')) {
            return $encryptedPassword; // No está cifrada, devolver tal cual
        }

        // Eliminar el prefijo "enc:"
        $encryptedData = substr($encryptedPassword, 4);

        // Usar la misma clave de cifrado que se usó para cifrar
        $encryptionKey = defined('CATALYST_KEY') ? CATALYST_KEY : 'default-encryption-key';

        // Descifrar usando openssl (método AES-256-CBC)
        $ivlen = openssl_cipher_iv_length('AES-256-CBC');
        $iv = substr(base64_decode($encryptedData), 0, $ivlen);
        $encrypted = substr(base64_decode($encryptedData), $ivlen);

        return openssl_decrypt($encrypted, 'AES-256-CBC', $encryptionKey, 0, $iv);
    }
}
