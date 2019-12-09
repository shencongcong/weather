<?php
/**
 * Created by PhpStorm.
 * User: danielshen
 * Date: 2019/8/21
 * Time: 14:41
 */

namespace Shencongcong\Ldap\Gateways;

use Shencongcong\Ldap\Support\Config;

class HomeGateway extends Gateway
{

    public function ldapCheck($username, $passport, Config $config)
    {
        $ret = ['auth'=>false];
        if ($username == "" || $passport == "") {
            return $ret;
        }
        $server = ldap_connect($config->get('url'));
        ldap_set_option($server, LDAP_OPT_NETWORK_TIMEOUT, 5);
        $res = ldap_bind($server, $username.'@intranet.123u.com', $passport);
        $ret = ['auth'=>$res];

        return $ret;
    }

    public function userSearch($username, Config $config)
    {
        $this->config = $config;
        if ( strlen($username) && preg_match("/^[\x{4e00}-\x{9fa5}a-zA-Z]+$/u", $username) ) {
            $ret = $this->ldap_user_search($username);
        }
        else {
            $ret = [];
        }

        return $ret;
    }

    public function ldap_user_search($username)
    {
        $server = ldap_connect($this->config->get('url'));
        ldap_set_option($server, LDAP_OPT_NETWORK_TIMEOUT, self::DEFAULT_TIMEOUT);
        ldap_set_option($server, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_bind($server, 'cn='.config('ldap.ldap_admin_account').',cn=Users,dc=intranet,dc=123u,dc=com', config('ldap.ldap_admin_password'));

        $filter = "(|(displayname=*$username*)(samaccountname=*$username*))";
        $base = 'OU=users,OU=123u,DC=intranet,DC=123u,DC=com';
        $base_array = explode(',', $base);
        $searcher = ldap_search($server, $base, $filter, [
            'telephonenumber',
            'cn',
            'displayname',
        ], 0);
        $entries = ldap_get_entries($server, $searcher);

        $users = [];
        foreach ($entries as $entry) {
            if ( is_array($entry) && isset($entry['dn']) ) {
                $dn_trunk = ldap_explode_dn($entry['dn'], 0);
                $dept_array = array_diff($dn_trunk, $base_array);

                array_shift($dept_array);
                array_shift($dept_array);

                $dept_name = '';
                foreach (array_reverse($dept_array) as $dept) {
                    //$dept_name .= '-' .	pack('H*', str_replace("\\", '', str_replace('OU=', '', $dept)));
                    $dept_name .= '-' . preg_replace_callback(
                            "/\\\([0-9A-Fa-f]{2})/",
                            function ($match) {
                                return chr(hexdec($match[1]));
                            },
                            str_replace('OU=', '', $dept)
                        );
                }
                $dept_name = substr($dept_name, 1);

                $users[] = [
                    'displayname' => $entry['displayname'][0],
                    'department' => $dept_name,
                    'telephonenumber' => $entry['telephonenumber'][0],
                ];
            }
        }
        ldap_close($server);

        return $users;
    }

    public function emailSearch($email, Config $config)
    {
        $this->config = $config;

        if ( strlen($email) && preg_match("/^[\x{4e00}-\x{9fa5}a-zA-Z]+$/u", $email) ) {
            $users = $this->ldap_mail_search($email);
            $ret = ['status' => 0, 'users' => $users];
        }
        else {
            $ret = ['status' => 1, 'msg' => []];
        }
        return $ret;
    }

    public function ldap_mail_search($mail)
    {
        $server = ldap_connect($this->config->get('url'));
        ldap_set_option($server, LDAP_OPT_NETWORK_TIMEOUT, self::DEFAULT_TIMEOUT);
        ldap_set_option($server, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_bind($server, 'cn='.config('ldap.ldap_admin_account').',cn=Users,dc=intranet,dc=123u,dc=com', config('ldap.ldap_admin_password'));
        $filter = "(mail=*$mail*)";
        $base = 'OU=users,OU=123u,DC=intranet,DC=123u,DC=com';
        $base_array = explode(',', $base);
        $searcher = ldap_search($server, $base, $filter, [
            'mail',
            'samaccountname',
            'displayname',
        ], 0);
        $entries = ldap_get_entries($server, $searcher);

        $users = [];
        foreach ($entries as $entry) {
            if ( is_array($entry) ) {
                $users[] = [
                    'samaccountname' => $entry['samaccountname'][0],
                    'displayname' => $entry['displayname'][0],
                    'mail' => $entry['mail'][0],
                ];
            }
        }
        ldap_close($server);
        return $users;
    }

    public function userAdd(Config $config,$username,$password)
    {
        $server=ldap_connect($config->get('url'));
        ldap_set_option($server, LDAP_OPT_NETWORK_TIMEOUT, self::DEFAULT_TIMEOUT);
        ldap_set_option($server, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_bind($server, 'cn='.config('ldap.ldap_admin_account').',cn=Users,dc=intranet,dc=123u,dc=com', config('ldap.ldap_admin_password'));
        //首先连接上服务器
        //$r=ldap_bind($ds,"cn=domadmin,o=jite","password");
        //系住一个管理员，有写的权限
        // cn=domadmin,o=jite顺序不能变
        $info["cn"]=$username; //必填
        $info["userpassword"]=$password;
        $info["location"]="shanghai";
        $info["objectclass"] = "person"; //必填person为个人，还有server…
        ldap_add($server, "cn=".$info["cn"].",o=jite", $info);
    }

    public function userDel()
    {
        
    }
    

}