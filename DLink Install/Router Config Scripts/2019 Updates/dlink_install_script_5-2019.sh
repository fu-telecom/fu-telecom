# Fuck You Telecom Install Script - May 2019
# Don't forget:
# chmod +x w_bhaul_dlink_install.sh

if [ $# -lt 5 ] ; then
        echo "Command requires arguments. <server_number> <skip_pkgs (1/0)> <set_root_password (1/0)> <chan 2.4ghz> <chan 5ghz>"
        exit 127
fi

SERVER_NUMBER=$1
SKIP_PKGS=$2
SET_ROOT_PASS=$3
CHAN24=$4
CHAN5=$5

echo "Executing Fuck You Telecom Mesh Router Install Script"
echo "=========================================="
echo "This script will fuck anyone who doesn't know how to use it. So fuck you."
echo ""
echo "Starting Setup in 3 seconds..."
echo ""
sleep 3

if [ $SET_ROOT_PASS -eq 1 ] ; then
        #Reset root password.
        echo -e "Setting Password\n"
        #Clear Pass
        passwd -d root
        #Reset Pass
        echo -e "n23n23129\nn23n23129\n" | passwd
        echo -e "\nPassword Set!\n"
fi

if [ $SKIP_PKGS -eq 0 ] ; then
        #**************************************************************
        #Install Packages and Other One Time Configs
        echo "Installing Packages"

        echo -e "\nChecking Internet Connection"

        if ping -c 1 8.8.8.8 > /dev/null
        then
                echo "Connection Found!"
                echo nothing > /dev/null
        else
                echo -e "\n\nERROR!!! No Internet Connection Present! ERROR!!!\n"
                exit 127
        fi

        opkg update

        opkg install babeld
        opkg install mini-snmpd
        opkg install kmod-ledtrig-default-on
        opkg install kmod-ledtrig-heartbeat
        opkg install kmod-ledtrig-morse
        opkg install kmod-ledtrig-netdev
        opkg install kmod-ledtrig-netfilter
        opkg install kmod-ledtrig-oneshot
        opkg install kmod-ledtrig-timer
        opkg install kmod-ledtrig-usbdev

        #**************************************************************
        # Add cb alias command to profile.
        echo "Adding Aliases to Profile"
        echo -e "alias cb='/etc/init.d/babeld status && tail -n 50 /var/log/babeld.log'" >> /etc/profile
        echo -e "alias rb='/etc/init.d/babeld restart'" >> /etc/profile
else
        echo "Skipping Package Installs"
        echo ""
fi

echo ""
echo "Setting Up Configuration Files..."
echo ""
sleep 3

#**************************************************************
#update /etc/banner
echo "Creating Banner"
echo "_______           _______  _                   _______" > /etc/banner
echo "(  ____ \|\     /|(  ____ \| \    /\  |\     /|(  ___  )|\     /|" >> /etc/banner
echo "| (    \/| )   ( || (    \/|  \  / /  ( \   / )| (   ) || )   ( |" >> /etc/banner
echo "| (__    | |   | || |      |  (_/ /    \ (_) / | |   | || |   | |" >> /etc/banner
echo "|  __)   | |   | || |      |   _ (      \   /  | |   | || |   | |" >> /etc/banner
echo "| (      | |   | || |      |  ( \ \      ) (   | |   | || |   | |" >> /etc/banner
echo "| )      | (___) || (____/\|  /  \ \     | |   | (___) || (___) |" >> /etc/banner
echo "|/       (_______)(_______/|_/    \/     \_/   (_______)(_______)" >> /etc/banner
echo -e "\n" >> /etc/banner
echo "                   - The Only Honest Telecom -" >> /etc/banner
echo "        ___________    .__" >> /etc/banner
echo "        \__    ___/___ |  |   ____   ____  ____   _____ " >> /etc/banner
echo "          |    |_/ __ \|  | _/ __ \_/ ___\/  _ \ /     \ " >> /etc/banner
echo "          |    |\  ___/|  |_\  ___/\  \__(  <_> )  Y Y  \ " >> /etc/banner
echo "          |____| \___  >____/\___  >\___  >____/|__|_|  / " >> /etc/banner
echo "                     \/          \/     \/            \/" >> /etc/banner
echo " -----------------------------------------------------------------" >> /etc/banner
echo "   * Fuck You Telecom Mesh Router #$SERVER_NUMBER" >> /etc/banner
echo "   * Mesh IP: 172.16.0.$SERVER_NUMBER/32" >> /etc/banner
echo "   * Local Net: 172.16.$SERVER_NUMBER.0/24" >> /etc/banner
echo "   * Local Net Gateway: 172.16.$SERVER_NUMBER.1/24" >> /etc/banner
echo "   * Ports '1' and '2': WIRED backhaul" >> /etc/banner
echo "   * WAN: Internet" >> /etc/banner
echo "   * WPS Enabled" >> /etc/banner
echo "   * Updated: May 2019" >> /etc/banner
echo "  -----------------------------------------------------------------" >> /etc/banner
echo "   * You have now tread upon the domain of the BOFH." >> /etc/banner
echo "   * Your life is forfeit. Make peace with your god(s)." >> /etc/banner
echo "  -----------------------------------------------------------------" >> /etc/banner

#**************************************************************
#update babeld conf
echo "Configuring Babel"
#mv /etc/config/babeld /etc/config/babeld.defaults

echo "package babeld" > /etc/config/babeld
echo "config general" >> /etc/config/babeld
echo "       option 'diversity' 'true'" >> /etc/config/babeld
echo "       option 'state-file' '/var/state/babeld'" >> /etc/config/babeld
echo -e "\n" >> /etc/config/babeld
echo "config interface" >> /etc/config/babeld
echo "        option 'update_interval' '30'" >> /etc/config/babeld
echo "        option 'hello_interval' '1'" >> /etc/config/babeld
echo -e "\n" >> /etc/config/babeld
echo "config interface" >> /etc/config/babeld
echo "        option 'ifname' 'backhaul'" >> /etc/config/babeld
echo "        option 'wired' 'true'" >> /etc/config/babeld
echo -e "\n" >> /etc/config/babeld
echo "config interface" >> /etc/config/babeld
echo "        option 'ifname' 'wlan_24'" >> /etc/config/babeld
echo "        option 'wired' 'false'" >> /etc/config/babeld
echo "           option 'link-quality' 'true'" >> /etc/config/babeld
echo -e "\n" >> /etc/config/babeld
echo "config interface" >> /etc/config/babeld
echo "        option 'ifname' 'wlan_5'" >> /etc/config/babeld
echo "        option 'wired' 'false'" >> /etc/config/babeld
echo "           option 'link-quality' 'true'" >> /etc/config/babeld
echo -e "\n" >> /etc/config/babeld
echo "config interface" >> /etc/config/babeld
echo "        option 'ifname' 'br-lan'" >> /etc/config/babeld
echo "        option 'wired' 'true'" >> /etc/config/babeld
echo -e "\n" >> /etc/config/babeld
echo "config interface" >> /etc/config/babeld
echo "        option 'ifname' 'wan'" >> /etc/config/babeld
echo "        option 'ignore' 'true'" >> /etc/config/babeld
echo -e "\n" >> /etc/config/babeld
echo "config filter" >> /etc/config/babeld
echo "        option 'type' 'redistribute'" >> /etc/config/babeld
echo "        option 'ip' '10.0.0.0/8'" >> /etc/config/babeld
echo "        option 'action' 'deny'" >> /etc/config/babeld
echo "" >> /etc/config/babeld:
echo "config filter" >> /etc/config/babeld
echo "        option 'type' 'in'" >> /etc/config/babeld
echo "        option 'ip' '10.0.0.0/8'" >> /etc/config/babeld
echo "        option 'action' 'deny'" >> /etc/config/babeld
echo "" >> /etc/config/babeld
echo "config filter" >> /etc/config/babeld
echo "        option 'type' 'out'" >> /etc/config/babeld
echo "        option 'ip' '10.0.0.0/8'" >> /etc/config/babeld
echo "        option 'action' 'deny'" >> /etc/config/babeld
echo "" >> /etc/config/babeld
echo "config filter" >> /etc/config/babeld
echo "       # Type" >> /etc/config/babeld
echo "       option 'type'   'redistribute'" >> /etc/config/babeld
echo "        # Selectors: ip, eq, le, ge, neigh, id, proto, local, if" >> /etc/config/babeld
echo "       option 'ip'     '0.0.0.0/0'" >> /etc/config/babeld
echo "       option 'eq'     '0'" >> /etc/config/babeld
echo "       option 'proto'  '3'" >> /etc/config/babeld
echo "        # Action" >> /etc/config/babeld
echo "       option 'action' 'metric 128'" >> /etc/config/babeld
echo "" >> /etc/config/babeld
echo "config filter" >> /etc/config/babeld
echo "        option 'type' 'in'" >> /etc/config/babeld
echo "        option 'ip' '0.0.0.0/0'" >> /etc/config/babeld
echo "        option 'action' 'metric 128'" >> /etc/config/babeld
echo "" >> /etc/config/babeld
echo "config filter" >> /etc/config/babeld
echo "        option 'type' 'out'" >> /etc/config/babeld
echo "        option 'ip' '0.0.0.0/0'" >> /etc/config/babeld
echo "        option 'action' 'metric 128'" >> /etc/config/babeld
echo "" >> /etc/config/babeld
echo "config filter" >> /etc/config/babeld
echo "        option 'type' 'redistribute'" >> /etc/config/babeld
echo "        option 'ip' '172.16.0.0/16'" >> /etc/config/babeld
echo "        option 'action' 'allow'" >> /etc/config/babeld
echo "" >> /etc/config/babeld
echo "config filter" >> /etc/config/babeld
echo "        option 'type' 'in'" >> /etc/config/babeld
echo "        option 'ip' '172.16.0.0/16'" >> /etc/config/babeld
echo "        option 'action' 'allow'" >> /etc/config/babeld
echo "" >> /etc/config/babeld
echo "config filter" >> /etc/config/babeld
echo "        option 'type' 'out'" >> /etc/config/babeld
echo "        option 'ip' '172.16.0.0/16'" >> /etc/config/babeld
echo "        option 'action' 'allow'" >> /etc/config/babeld
echo "" >> /etc/config/babeld
echo "config filter" >> /etc/config/babeld
echo "        option 'type' 'redistribute'" >> /etc/config/babeld
echo "        option 'ip' '172.16.0.$SERVER_NUMBER/32'" >> /etc/config/babeld
echo "        option 'action' 'allow'" >> /etc/config/babeld
echo "        option 'local' '1'" >> /etc/config/babeld
echo "" >> /etc/config/babeld
echo "# Everything else is DENIED" >> /etc/config/babeld
echo "config filter" >> /etc/config/babeld
echo "       option 'type' 'in'" >> /etc/config/babeld
echo "       option 'action' 'deny'" >> /etc/config/babeld
echo "" >> /etc/config/babeld
echo "config filter" >> /etc/config/babeld
echo "       option 'type' 'redistribute'" >> /etc/config/babeld
echo "       option 'local' '1'" >> /etc/config/babeld
echo "       option 'action' 'deny'" >> /etc/config/babeld
echo "" >> /etc/config/babeld
echo "config filter" >> /etc/config/babeld
echo "       option 'type' 'redistribute'" >> /etc/config/babeld
echo "       option 'action' 'deny'" >> /etc/config/babeld



#****************************************************************************
#Wireless Configs
echo "Configuring Wireless"
echo "config wifi-device  radio0" > /etc/config/wireless
echo "        option type     mac80211" >> /etc/config/wireless
echo "        option channel  $CHAN24" >> /etc/config/wireless
echo "        option hwmode   11ng" >> /etc/config/wireless
echo "        option path     'platform/ar934x_wmac'" >> /etc/config/wireless
#Was originally HT40+, but in 2.4 needs to be HT20
#echo "        option htmode   'HT40'" >> /etc/config/wireless
echo "        option htmode   'HT20'" >> /etc/config/wireless
#echo "        list 'ht_capab' 'SHORT-GI-40'" >> /etc/config/wireless
#echo "        list 'ht_capab' 'TX-STBC'" >> /etc/config/wireless
#echo "        list 'ht_capab' 'RX-STBC1'" >> /etc/config/wireless
#echo "        list 'ht_capab' 'DSSS_CCK-40'" >> /etc/config/wireless
echo "        option bursting '1'" >> /etc/config/wireless
echo "        option ff '1'" >> /etc/config/wireless
echo "        option compression '1'" >> /etc/config/wireless
echo "        option noscan '1'" >> /etc/config/wireless
echo "        # REMOVE THIS LINE TO ENABLE WIFI:" >> /etc/config/wireless
echo "#       option disabled 1" >> /etc/config/wireless
echo "" >> /etc/config/wireless
echo "config wifi-iface" >> /etc/config/wireless
echo "        option device   radio0" >> /etc/config/wireless
echo "        option network  wlan_24" >> /etc/config/wireless
echo "        option mode     adhoc" >> /etc/config/wireless
echo "        option ssid     mesh.fuckyou" >> /etc/config/wireless
echo "        option encryption none" >> /etc/config/wireless
echo "" >> /etc/config/wireless
echo "config wifi-iface" >> /etc/config/wireless
echo "        option device   radio0" >> /etc/config/wireless
echo "        option network  lan" >> /etc/config/wireless
echo "        option mode     ap" >> /etc/config/wireless
echo "        option ssid     'Fuck You'" >> /etc/config/wireless
echo "        option wds      '1'" >> /etc/config/wireless
echo "        option encryption none" >> /etc/config/wireless
echo "" >> /etc/config/wireless
echo "config wifi-iface" >> /etc/config/wireless
echo "        option device   radio0" >> /etc/config/wireless
echo "        option network  lan" >> /etc/config/wireless
echo "        option mode     ap" >> /etc/config/wireless
echo "        option ssid     'FU24-$SERVER_NUMBER'" >> /etc/config/wireless
echo "        option encryption none" >> /etc/config/wireless
echo "" >> /etc/config/wireless
echo "config wifi-device  radio1" >> /etc/config/wireless
echo "        option type     mac80211" >> /etc/config/wireless
echo "        option channel  $CHAN5" >> /etc/config/wireless
echo "        option hwmode   11na" >> /etc/config/wireless
echo "        option path     'pci0000:00/0000:00:00.0'" >> /etc/config/wireless
echo "        option htmode   'HT40+'" >> /etc/config/wireless
echo "        list 'ht_capab' 'SHORT-GI-40'" >> /etc/config/wireless
echo "        list 'ht_capab' 'TX-STBC'" >> /etc/config/wireless
echo "        list 'ht_capab' 'RX-STBC1'" >> /etc/config/wireless
echo "        list 'ht_capab' 'DSSS_CCK-40'" >> /etc/config/wireless
echo "        option bursting '1'" >> /etc/config/wireless
echo "        option ff '1'" >> /etc/config/wireless
echo "        option compression '1'" >> /etc/config/wireless
echo "        option noscan '1'" >> /etc/config/wireless
echo "" >> /etc/config/wireless
echo "# REMOVE THIS LINE TO ENABLE WIFI:" >> /etc/config/wireless
echo "#       option disabled 1" >> /etc/config/wireless
echo "" >> /etc/config/wireless
echo "config wifi-iface" >> /etc/config/wireless
echo "        option device   radio1" >> /etc/config/wireless
echo "        option network  wlan_5" >> /etc/config/wireless
echo "        option mode     adhoc" >> /etc/config/wireless
echo "        option ssid     mesh.fuckyou" >> /etc/config/wireless
echo "        option encryption none" >> /etc/config/wireless
echo "" >> /etc/config/wireless
echo "config wifi-iface" >> /etc/config/wireless
echo "        option device   radio1" >> /etc/config/wireless
echo "        option network  lan" >> /etc/config/wireless
echo "        option mode     ap" >> /etc/config/wireless
echo "        option ssid     'FU5-$SERVER_NUMBER'" >> /etc/config/wireless
echo "        option encryption none" >> /etc/config/wireless

#***************************************************************************
#Network Configs
echo "Configuring Network"
#mv /etc/config/network /etc/config/network.defaults

echo "config interface 'loopback'" > /etc/config/network
echo "        option ifname 'lo'" >> /etc/config/network
echo "        option proto 'static'" >> /etc/config/network
echo "        option ipaddr '127.0.0.1'" >> /etc/config/network
echo "        option netmask '255.0.0.0'" >> /etc/config/network
echo "" >> /etc/config/network
echo "config globals 'globals'" >> /etc/config/network
echo "       option ula_prefix 'fde9:eb3a:ac8a::/48'" >> /etc/config/network
echo "" >> /etc/config/network
echo "config interface 'lan'" >> /etc/config/network
echo "        option ifname 'eth0.1'" >> /etc/config/network
echo "        option force_link '1'" >> /etc/config/network
echo "        option type 'bridge'" >> /etc/config/network
echo "        option proto 'static'" >> /etc/config/network
echo "        option ipaddr '172.16.$SERVER_NUMBER.1'" >> /etc/config/network
echo "        option netmask '255.255.255.0'" >> /etc/config/network
echo "        option ip6assign '60'" >> /etc/config/network
echo "        option ip6hint '$SERVER_NUMBER'" >> /etc/config/network
echo "" >> /etc/config/network
echo "config interface 'wan'" >> /etc/config/network
echo "        option ifname 'eth0.2'" >> /etc/config/network
echo "        option proto 'dhcp'" >> /etc/config/network
echo "" >> /etc/config/network
echo "config interface 'backhaul'" >> /etc/config/network
 echo "        option ifname 'eth0.3'" >> /etc/config/network
echo "        option proto 'static'" >> /etc/config/network
echo "        option ipaddr '172.16.0.$SERVER_NUMBER'" >> /etc/config/network
echo "        option netmask '255.255.255.255'" >> /etc/config/network
echo "        option ip6assign '60'" >> /etc/config/network
echo "        option ip6hint '$SERVER_NUMBER$SERVER_NUMBER'" >> /etc/config/network
echo "" >> /etc/config/network
echo "config interface 'wlan_24'" >> /etc/config/network
echo "        option proto 'static'" >> /etc/config/network
echo "        option ipaddr '172.16.0.$SERVER_NUMBER'" >> /etc/config/network
echo "        option netmask '255.255.255.255'" >> /etc/config/network
echo "        option ip6assign '60'" >> /etc/config/network
echo "        option ip6hint '$SERVER_NUMBER$SERVER_NUMBER'" >> /etc/config/network
echo "" >> /etc/config/network
echo "config interface 'wlan_5'" >> /etc/config/network
echo "        option proto 'static'" >> /etc/config/network
echo "        option ipaddr '172.16.0.$SERVER_NUMBER'" >> /etc/config/network
echo "        option netmask '255.255.255.255'" >> /etc/config/network
echo "        option ip6assign '60'" >> /etc/config/network
echo "        option ip6hint '$SERVER_NUMBER$SERVER_NUMBER'" >> /etc/config/network
echo "" >> /etc/config/network
echo "config switch" >> /etc/config/network
echo "        option name 'switch0'" >> /etc/config/network
echo "        option reset '1'" >> /etc/config/network
echo "        option enable_vlan '1'" >> /etc/config/network
echo "" >> /etc/config/network
echo "config switch_vlan" >> /etc/config/network
echo "        option device 'switch0'" >> /etc/config/network
echo "        option vlan '1'" >> /etc/config/network
echo "        option ports '0t 1 2'" >> /etc/config/network
echo "" >> /etc/config/network
echo "config switch_vlan" >> /etc/config/network
echo "        option device 'switch0'" >> /etc/config/network
echo "        option vlan '2'" >> /etc/config/network
echo "        option ports '0t 5'" >> /etc/config/network
echo "" >> /etc/config/network
echo "config switch_vlan" >> /etc/config/network
echo "        option device 'switch0'" >> /etc/config/network
echo "        option vlan '3'" >> /etc/config/network
echo "        option ports '0t 3 4'" >> /etc/config/network
#****************************************************************************
#System Configs
echo "Configuring System."
echo "config system" > /etc/config/system
echo "        option hostname $SERVER_NUMBER-mesh.fuckyou" >> /etc/config/system
echo "        option timezone UTC" >> /etc/config/system
echo "" >> /etc/config/system
echo "config timeserver ntp" >> /etc/config/system
echo "        list server     main.fuckyou" >> /etc/config/system
echo "        list server     backup.fuckyou" >> /etc/config/system
echo "        option enabled 1" >> /etc/config/system
echo "        option enable_server 0" >> /etc/config/system
echo "" >> /etc/config/system
echo "config 'led'" >> /etc/config/system
echo "        option 'sysfs' 'd-link:blue:wps'" >> /etc/config/system
echo "        option 'trigger' 'heartbeat'" >> /etc/config/system
echo "        option 'default' '1'" >> /etc/config/system
echo "" >> /etc/config/system
echo "config 'led'" >> /etc/config/system
echo "        option 'sysfs' 'd-link:green:power'" >> /etc/config/system
echo "        option 'trigger' 'netdev'" >> /etc/config/system
echo "        option 'dev' 'eth0.2'" >> /etc/config/system
echo "        option 'mode' 'link'" >> /etc/config/system
echo "" >> /etc/config/system
echo "config 'led'" >> /etc/config/system
echo "        option 'sysfs' 'd-link:amber:power'" >> /etc/config/system
echo "        option 'trigger' 'netdev'" >> /etc/config/system
echo "        option 'dev' 'eth0.2'" >> /etc/config/system
echo "        option 'mode' 'tx rx'" >> /etc/config/system
echo "" >> /etc/config/system
echo "config 'led'" >> /etc/config/system
echo "        option 'sysfs' 'd-link:amber:planet'" >> /etc/config/system
echo "        option 'trigger' 'phy1rx'" >> /etc/config/system
echo "        option 'default' '0'" >> /etc/config/system
echo "" >> /etc/config/system
echo "config 'led'" >> /etc/config/system
echo "        option 'sysfs' 'd-link:amber:planet'" >> /etc/config/system
echo "        option 'trigger' 'phy1tx'" >> /etc/config/system
echo "        option 'default' '0'" >> /etc/config/system
echo "" >> /etc/config/system
echo "config 'led'" >> /etc/config/system
echo "        option 'sysfs' 'd-link:green:planet'" >> /etc/config/system
echo "        option 'trigger' 'phy0rx'" >> /etc/config/system
echo "        option 'default' '0'" >> /etc/config/system
echo "" >> /etc/config/system
echo "config 'led'" >> /etc/config/system
echo "        option 'sysfs' 'd-link:green:planet'" >> /etc/config/system
echo "        option 'trigger' 'phy0tx'" >> /etc/config/system
echo "        option 'default' '0'" >> /etc/config/system

#****************************************************************************
#DHCP Configs
echo "Configuring DHCP / DNS"
echo "config dnsmasq" > /etc/config/dhcp
echo "        option domainneeded '1'" >> /etc/config/dhcp
echo "        option boguspriv '1'" >> /etc/config/dhcp
echo "        option filterwin2k '0'" >> /etc/config/dhcp
echo "        option localise_queries '1'" >> /etc/config/dhcp
echo "        option rebind_protection '1'" >> /etc/config/dhcp
echo "        option rebind_localhost '1'" >> /etc/config/dhcp
echo "        option local '/lan/'" >> /etc/config/dhcp
echo "        option domain 'fuckyou'" >> /etc/config/dhcp
echo "        option expandhosts '1'" >> /etc/config/dhcp
echo "        option nonegcache '0'" >> /etc/config/dhcp
echo "        option authoritative '1'" >> /etc/config/dhcp
echo "        option readethers '1'" >> /etc/config/dhcp
echo "        option leasefile '/tmp/dhcp.leases'" >> /etc/config/dhcp
echo "        option resolvfile '/tmp/resolv.conf.auto'" >> /etc/config/dhcp
echo "" >> /etc/config/dhcp
echo "config dhcp 'lan'" >> /etc/config/dhcp
echo "        option interface 'lan'" >> /etc/config/dhcp
echo "        option start '100'" >> /etc/config/dhcp
echo "        option limit '150'" >> /etc/config/dhcp
echo "        option leasetime '12h'" >> /etc/config/dhcp
echo "        option dhcpv6 'server'" >> /etc/config/dhcp
echo "        option ra 'server'" >> /etc/config/dhcp
echo "        list 'dhcp_option' '6,172.16.1.2,172.16.1.3'" >> /etc/config/dhcp
echo "        list 'dhcp_option' '66,172.16.1.2'" >> /etc/config/dhcp
echo "#        list 'dhcp_option' '150,172.16.1.3'" >> /etc/config/dhcp
echo "" >> /etc/config/dhcp
echo "config dhcp 'wan'" >> /etc/config/dhcp
echo "        option interface 'wan'" >> /etc/config/dhcp
echo "        option ignore '1'" >> /etc/config/dhcp
echo "" >> /etc/config/dhcp
echo "config dhcp 'backhaul'" >> /etc/config/dhcp
echo "        option interface 'backhaul'" >> /etc/config/dhcp
echo "        option ignore '1'" >> /etc/config/dhcp
echo "" >> /etc/config/dhcp
echo "config odhcpd 'odhcpd'" >> /etc/config/dhcp
echo "        option maindhcp '0'" >> /etc/config/dhcp
echo "        option leasefile '/tmp/hosts/odhcpd'" >> /etc/config/dhcp
echo "        option leasetrigger '/usr/sbin/odhcpd-update'" >> /etc/config/dhcp

#****************************************************************************
#Firewall Configs
echo "Configuring Firewall"
echo "config defaults" > /etc/config/firewall
echo "        option syn_flood        1" >> /etc/config/firewall
echo "        option input            ACCEPT" >> /etc/config/firewall
echo "        option output           ACCEPT" >> /etc/config/firewall
echo "        option forward          ACCEPT" >> /etc/config/firewall


#**************************************************
# Cron Tab Jobs / Network Keepalive
echo "Configuring Network Keepalive Cron Job"

echo "#Run Network Check Every 5 Minutes" > /root/network_check_crontab
echo "3 * * * * /root/network_check" >> /root/network_check_crontab

#echo "if ping -c 1 172.16.1.1 > /dev/null" > /root/network_check
#echo "then" >> /root/network_check
#echo "        echo nothing > /dev/null" >> /root/network_check
#echo "else" >> /root/network_check
#echo "        /etc/init.d/babeld reload" >> /root/network_check
#echo "" >> /root/network_check
#echo "        sleep 180" >> /root/network_check
#echo "" >> /root/network_check
#echo "        if ping -c 1 172.16.1.1 > /dev/null" >> /root/network_check
#echo "        then" >> /root/network_check
#echo "                echo nothing > /dev/null" >> /root/network_check
#echo "        else" >> /root/network_check
#echo "                /etc/init.d/network restart" >> /root/network_check
#echo "                sleep 5" >> /root/network_check
#echo "                /etc/init.d/babeld restart" >> /root/network_check
#echo "        fi" >> /root/network_check
#echo "" >> /root/network_check
#echo "fi" >> /root/network_check

echo "if ping -c 1 172.16.1.1 > /dev/null" > /root/network_check
echo "then" >> /root/network_check
echo "        echo nothing > /dev/null" >> /root/network_check
echo "else" >> /root/network_check
echo "        /etc/init.d/babeld reload" >> /root/network_check
echo "fi" >> /root/network_check

chmod +x /root/network_check

crontab /root/network_check_crontab

#**************************************************
# Updating Resolv.conf
echo "Updating resolv.conf with FUT DNS Server Address"

echo "search fuckyou" > /etc/resolv.conf
echo "nameserver 172.16.1.2" >> /etc/resolv.conf

#**************************************************
# Adding Babel Fast Startup Fix

echo "/etc/init.d/babeld reload" > /etc/rc.local
echo "exit 0" >> /etc/rc.local

#********************************************************************
#Enable Startup Daemons
echo "Enabling Daemons"

/etc/init.d/cron enable
/etc/init.d/cron start

/etc/init.d/babeld enable
/etc/init.d/babeld reload
/etc/init.d/babeld start
/etc/init.d/babeld reload

echo ""
echo "Finished Fucking Your Install"
echo ""
echo ""
echo "Rebooting in 5..."
echo ""
sleep 1
echo "4..."
echo ""
sleep 1
echo "3..."
echo ""
sleep 1
echo "2..."
echo ""
sleep 1
echo "1..."
sleep 1
reboot
