from pathlib import Path
import re
entries = [
    ('app/Views/layout/template.php', '<style>', '</style>', 'public/assets/css/style.css'),
    ('app/Views/auth/login.php', '<style>', '</style>', 'public/assets/css/auth.css'),
    ('app/Views/home/index.php', '<script>', '</script>', 'public/assets/js/home.js'),
]
for src, start, end, out in entries:
    text = Path(src).read_text(encoding='utf-8')
    match = re.search(re.escape(start) + r'(.*?)' + re.escape(end), text, re.S)
    if match:
        Path(out).write_text(match.group(1).strip() + '\n', encoding='utf-8')
        print(f'Wrote {out}')
    else:
        print(f'No match for {src} between {start} and {end}')
