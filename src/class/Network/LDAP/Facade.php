<?php
namespace Nora\Network\LDAP;

use Nora\Base\Event;
use Nora\Base\Component\Component;
use Nora\Network\API\Twitter;
use Nora;

/**
 * LDAP
 * ====
 *
 * 使い方
 * ------
 *
 * $ldap->setup([
 *  'host' => 'openldap.local'
 * ]);
 * 
 * try
 * {
 *  $con = $ldap->connect()->bind('cn=root,dc=hajime,dc=work', 'deganjue');
 *  
 *  // Kurariの公開鍵を取り出す
 *  $res = $con->search('dc=hajime,dc=work','uid=kurari',['sshPublicKey'])->map(function($e) {
 *    return $e['sshpublickey'][0];
 *  });
 * 
 * }catch(LDAPException $e) {
 *  echo $e;
 * }
 *
 * $user = 'kurari';
 * $pass = 'deganjue';
 *
 * try
 * {
 *  $ldap->connect()->bind("uid=$user,ou=People,dc=hajime,dc=work", $pass));
 *  // 認証成功
 * }catch(LDAPException $e) {
 *  // 認証失敗
 * }
 *
 * // 継展を追加
 * $ldap_con->add('dc=tsuguten,dc=hajime,dc=work', [
 *     'dc' => 'tsuguten',
 *     'o' => '継展',
 *     'objectclass' => [
 *         'dcObject',
 *         'organization'
 *     ]
 * ]);
 * 
 * // 継展メンバーを追加
 * $ldap_con->add('ou=people,dc=tsuguten,dc=hajime,dc=work', [
 *     'ou' => 'people',
 *     'objectclass' => [
 *         'organizationalUnit',
 *     ]
 * ]);
 * $ldap_con->add('uid=shiho,ou=people,dc=tsuguten,dc=hajime,dc=work', [
 *     'cn' => 'shiho',
 *     'sn' => 'sakamoto',
 *     'userPassword' => '{CRYPT}'.crypt('hoge'),
 *     'objectclass' => [
 *         'inetOrgPerson'
 *     ]
 * ]);
 */
class Facade extends Component
{
    protected function initComponentImpl( )
    {
    }

    public function setup($settings)
    {
        if (!isset($settings['host']))
        {
            throw new LDAPException(Nora::message('ホスト名が設定されていません'));
        }

        $this->_host = $settings['host'];
        $this->_port = 
            isset($settings['port']) ?
            $settings['port'] :
            389;
    }

    public function connect ( )
    {
        return new Connection($host = $this->_host, $this->_port);
    }
}
