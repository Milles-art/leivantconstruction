@echo OFF
REM Leivant local dev server (XAMPP PHP + MySQL must be running)
cd /d "C:\Users\DIGITAL STAR\leivant-site-backup\public_html"
"C:\xampp\php\php.exe" -S 127.0.0.1:8000 -t "C:\Users\DIGITAL STAR\leivant-site-backup\public_html"
