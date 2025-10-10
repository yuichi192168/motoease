Write-Host "=== Resetting local BPSMS project ==="

# Stop Apache and MySQL services (if needed)
Write-Host "Stopping Apache and MySQL..."
Start-Process "C:\xampp\xampp_stop.exe" -Wait

# Clear temp or cache folders
Write-Host "Clearing cache and temporary files..."
Remove-Item -Recurse -Force "C:\xampp\htdocs\bpsms\storage\framework\cache" -ErrorAction SilentlyContinue
Remove-Item -Recurse -Force "C:\xampp\htdocs\bpsms\storage\framework\sessions" -ErrorAction SilentlyContinue

# Restart XAMPP
Write-Host "Starting Apache and MySQL..."
Start-Process "C:\xampp\xampp_start.exe"

Write-Host "=== Reset complete! ==="