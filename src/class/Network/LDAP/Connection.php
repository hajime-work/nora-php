<?php
namespace Nora\Network\LDAP;

use Nora\Base\Event;
use Nora\Base\Component\Component;
use Nora\Network\API\Twitter;
use Nora;

/**
 * LDAP: Connection
 */
class Connection
{
    public function __construct($host, $port = 389)
    {
        $this->_con = ldap_connect($host, $port);
        if (false === $con = ldap_connect($host, $port))
        {
            throw new LDAPException(Nora::Message('接続出来ませんでした'));
        }

        ldap_set_option($this->_con, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($this->_con, LDAP_OPT_REFERRALS, 0);

    }

    public function con()
    {
        return $this->_con;
    }

    public function bind($dn, $secret = null)
    {
        if(false === ldap_bind($this->con(), $dn, $secret) )
        {
            throw new LDAPException(
                Nora::Message(
                    'Bind出来ませんでした: (%s) %s', [
                        ldap_errno($this->con()),
                        ldap_error($this->con())
                    ]
                ));
        }
        return $this;
    }

    public function search($dn, $filter ='(objectclass=*)', $attrs = null)
    {
        // リソースを取得
        if ($attrs != null)
        {
            $result = ldap_search($this->con(),  $dn, $filter, $attrs);
        }else{
            $result = ldap_search($this->con(),  $dn, $filter);
        }

        // イテレータ
        return new Entries($this->con(), $result);
    }

    public function add($dn, $info)
    {
        if (false === ldap_add($this->con(), $dn, $info))
        {
            throw new LDAPException(
                Nora::Message(
                    "追加失敗: %s (%s) %s %s", [
                        $dn,
                        ldap_errno($this->con()),
                        ldap_error($this->con()),
                        var_export($info, true)
                    ]
                )
            );
        }
        return $this;
    }
}
