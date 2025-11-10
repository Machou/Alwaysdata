import socket
import sys

def check_port(ip, port, service):
    try:
        sock = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
        sock.settimeout(2)
        sock.connect((ip, port))
        banner = sock.recv(1024)
        print(f"Port {port}|{service} is open: {banner.decode().strip()}")
        sock.close()
    except Exception as e:
        print(f"Port {port}|{service} is closed or filtered: {str(e)}")

def is_valid_ip(ip: str) -> bool:
    parts = ip.split('.')

    if len(parts) != 4:
        return False

    for part in parts:
        if not part.isdigit():
            return False

        num = int(part)

        if num < 0 or num > 255:
            return False

    return True

if __name__ == "__main__":
    if len(sys.argv) != 2:
        print("Utilisation : python script.py <adresse_ipv4>")
        sys.exit(1)

    ip = sys.argv[1]

    if not is_valid_ip(ip):
        print("Adresse IP incorrecte")
        sys.exit(1)

    ports = [
        {"port": 21, "service": "FTP"},
        {"port": 22, "service": "SSH"},
        {"port": 23, "service": "Telnet"},
        {"port": 25, "service": "SMTP"},
        # {"port": 53, "service": "DNS"},
        {"port": 80, "service": "HTTP"},
        {"port": 110, "service": "POP3"},
        {"port": 139, "service": "NETBIOS"},
        {"port": 143, "service": "IMAP"},
        {"port": 443, "service": "HTTPS"},
        {"port": 445, "service": "SMB"},
        {"port": 1433, "service": "MSSQL"},
        # {"port": 1521, "service": "ORACLE"},
        {"port": 3306, "service": "MySQL"},
        {"port": 3389, "service": "Remote Desktop"},
    ]

    for item in ports:
        port = item["port"]
        service = item["service"]
        check_port(ip, port, service)
