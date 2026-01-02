$port = 3306
Write-Host "Checking MySQL connection..."
$tcpConnection = Test-NetConnection -ComputerName localhost -Port $port -InformationLevel Quiet

if ($tcpConnection) {
    Write-Host "✅ MySQL is online. Restoring Admin user..." -ForegroundColor Green
    & "c:\xampp\php\php.exe" "restore_admin_cli.php"
} else {
    Write-Host "❌ MySQL is OFFLINE." -ForegroundColor Red
    Write-Host "Fadlan fur XAMPP Control Panel oo 'Start' dheh MySQL." -ForegroundColor Yellow
    Write-Host "(Please open XAMPP Control Panel and start MySQL)." -ForegroundColor Gray
}
