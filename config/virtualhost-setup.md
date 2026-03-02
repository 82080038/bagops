# Setup VirtualHost untuk Akses Jaringan Lokal

## Edit Apache Config (XAMPP)

Edit file: /opt/lampp/etc/extra/httpd-vhosts.conf

```apache
<VirtualHost *:80>
    ServerName bagops.local
    DocumentRoot "/opt/lampp/htdocs/bagops"
    
    <Directory "/opt/lampp/htdocs/bagops">
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog logs/bagops_error_log
    CustomLog logs/bagops_access_log common
</VirtualHost>
```

## Restart Apache
```bash
sudo /opt/lampp/bin/httpd -k restart
# Atau melalui XAMPP Control Panel
```

## Firewall Setup (Linux)
```bash
# Allow Apache port 80
sudo ufw allow 80
sudo ufw allow 443

# Atau iptables
sudo iptables -A INPUT -p tcp --dport 80 -j ACCEPT
```

## Akses dari Komputer Lain
1. Pastikan komputer lain di jaringan WiFi/LAN yang sama
2. Akses: http://10.3.141.130/bagops
3. Atau jika pakai VirtualHost: http://10.3.141.130
