from pathlib import Path

root = Path('c:/xampp/htdocs/TokoBajuCustom')

# Helper to replace a text block and fail if not found.
def replace_block(path: Path, old: str, new: str):
    text = path.read_text(encoding='utf-8')
    if old not in text:
        raise ValueError(f'Block not found in {path}: {old[:60]!r}')
    path.write_text(text.replace(old, new, 1), encoding='utf-8')

# 1) Update layout/template.php
layout = root / 'app/Views/layout/template.php'
layout_text = layout.read_text(encoding='utf-8')
layout_text = layout_text.replace(
    "<title><?= $title ?? 'SIMONSTER — One of One Wearable Art' ?></title>",
    "<title><?= $title ?? 'BATOM — One of One Wearable Art' ?></title>"
)
layout_text = layout_text.replace(
    '<div class="loader-logo">SIMONSTER</div>',
    '<div class="loader-logo">BATOM</div>'
)
old_block = "    <link rel=\"stylesheet\" href=\"https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css\">\n    \n    <style>"
new_block = "    <link rel=\"stylesheet\" href=\"https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css\">\n    <link rel=\"stylesheet\" href=\"<?= base_url('assets/css/style.css') ?>\">\n    <?= $this->renderSection('styles') ?>"
if old_block not in layout_text:
    raise ValueError('Style block marker not found in template.php')
layout_text = layout_text.replace(old_block, new_block)
layout_text = layout_text.replace('    </style>\n\n    <?= $this->renderSection(\'styles\') ?>', '    <?= $this->renderSection(\'styles\') ?>')
layout.write_text(layout_text, encoding='utf-8')

# 2) Update home/index.php script block and branding
home = root / 'app/Views/home/index.php'
home_text = home.read_text(encoding='utf-8')
if '<?= $this->section(\'scripts\') ?>\n    <script>' in home_text:
    home_text = home_text.replace(
        "<?= $this->section('scripts') ?>\n    <script>",
        "<?= $this->section('scripts') ?>\n    <script src=\"<?= base_url('assets/js/home.js') ?>\"></script>\n"
    )
    # remove trailing script block if still present after replacement
    start = home_text.find('<script src="<?= base_url(\'assets/js/home.js\') ?>"></script>\n')
    if start != -1:
        end = home_text.rfind('</script>')
        if end > start:
            home_text = home_text[:start + len('<script src="<?= base_url(\'assets/js/home.js\') ?>\"></script>\n')] + home_text[end+len('</script>'):]
else:
    raise ValueError('Home script block marker not found')
# Branding replacements
home_text = home_text.replace('SIMONSTER<span>1:1</span>', 'BATOM<span>1:1</span>')
home_text = home_text.replace('SIMONSTER original, you are carrying a piece of a living museum.', 'BATOM original, you are carrying a piece of a living museum.')
home_text = home_text.replace('Simonster stands as a defiant monument against the sterile tide of modern fast fashion and repetitive production loops.', 'Batom stands as a defiant monument against the sterile tide of modern fast fashion and repetitive production loops.')
home_text = home_text.replace('<p>Explore daily process reels @simonster.11</p>', '<p>Explore daily process reels @batom.11</p>')
home_text = home_text.replace('<span class="aesthetic-card-author">— Head Artisan, SIMONSTER</span>', '<span class="aesthetic-card-author">— Head Artisan, Batom</span>')
home.write_text(home_text, encoding='utf-8')

# 3) Update auth/login.php to load external auth assets and use form id for JS hooks
login = root / 'app/Views/auth/login.php'
login_text = login.read_text(encoding='utf-8')
login_text = login_text.replace('<title>Login - SIMONSTER</title>', '<title>Login - BATOM</title>')
login_text = login_text.replace(
    '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">\n    \n    <style>',
    '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">\n    <link rel="stylesheet" href="<?= base_url(\'assets/css/auth.css\') ?>">\n\n    <style>'
)
# remove the big inline style block by replacing last occurrence of </style> before </head>
head_close = login_text.rfind('</style>')
head_end = login_text.find('</head>', head_close)
if head_close != -1 and head_end != -1 and head_close < head_end:
    login_text = login_text[:login_text.find('<style>', 0, head_close)] + login_text[head_end:]
# Add external auth.js before closing body
script_index = login_text.rfind('<script>')
if script_index != -1:
    login_text = login_text[:script_index] + '    <script src="<?= base_url(\'assets/js/auth.js\') ?>"></script>\n</body>\n</html>'
# add id to sign in form
login_text = login_text.replace('<form action="<?= base_url(\'auth/process\') ?>" method="POST" class="auth-form">', '<form id="signin-interactive-form" action="<?= base_url(\'auth/process\') ?>" method="POST" class="auth-form">')
login.write_text(login_text, encoding='utf-8')

# 4) Replace Auth controller contents with DB-backed auth and register support
auth = root / 'app/Controllers/Auth.php'
auth_text = '''<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    public function login()
    {
        return view('auth/login');
    }

    public function process()
    {
        $session = session();
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $userModel = new UserModel();
        $user = $userModel->findByEmail($email);

        if (! $user || ! password_verify($password, $user['password'])) {
            $session->setFlashdata('error', 'Invalid email or password. Please try again.');
            return redirect()->back()->withInput();
        }

        $session->set([
            'isLoggedIn' => true,
            'userId' => $user['id'],
            'userName' => $user['name'],
            'userEmail' => $user['email'],
        ]);

        $session->setFlashdata('success', 'Welcome back, ' . esc($user['name']) . '.');
        return redirect()->to(base_url('/'));
    }

    public function register()
    {
        $session = session();
        $name = $this->request->getPost('name');
        $username = $this->request->getPost('username');
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');
        $confirm = $this->request->getPost('confirm_password');

        if ($password !== $confirm) {
            $session->setFlashdata('error', 'Passwords do not match. Please confirm again.');
            return redirect()->back()->withInput();
        }

        $userModel = new UserModel();
        if ($userModel->findByEmail($email)) {
            $session->setFlashdata('error', 'An account with that email already exists.');
            return redirect()->back()->withInput();
        }

        $userModel->save([
            'name' => $name,
            'username' => $username,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
        ]);

        $session->setFlashdata('success', 'Account created successfully. Please sign in.');
        return redirect()->to(base_url('/login'));
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to(base_url('/'));
    }
}
'''
auth.write_text(auth_text, encoding='utf-8')

# 5) Add /auth/register route if missing
routes = root / 'app/Config/Routes.php'
routes_text = routes.read_text(encoding='utf-8')
if "$routes->post('/auth/register', 'Auth::register');" not in routes_text:
    routes_text = routes_text.replace("$routes->post('/auth/process', 'Auth::process'); // Validasi login\n", "$routes->post('/auth/process', 'Auth::process'); // Validasi login\n$routes->post('/auth/register', 'Auth::register'); // Register baru\n")
routes.write_text(routes_text, encoding='utf-8')

# 6) Add filter aliases and class files
filters = root / 'app/Config/Filters.php'
filters_text = filters.read_text(encoding='utf-8')
if "'authCustomer'  => AuthCustomer::class" not in filters_text:
    filters_text = filters_text.replace("        'cors'          => Cors::class,\n", "        'cors'          => Cors::class,\n        'authCustomer'  => \App\Filters\AuthCustomer::class,\n        'authAdmin'     => \App\Filters\AuthAdmin::class,\n")
    filters.write_text(filters_text, encoding='utf-8')

filters_dir = root / 'app/Filters'
filters_dir.mkdir(parents=True, exist_ok=True)
for name in ['AuthCustomer.php', 'AuthAdmin.php']:
    path = filters_dir / name
    if not path.exists():
        class_name = name.replace('.php', '')
        path.write_text(f'''<?php

namespace App\\Filters;

use CodeIgniter\\HTTP\\RequestInterface;
use CodeIgniter\\HTTP\\ResponseInterface;
use CodeIgniter\\Filters\\FilterInterface;

class {class_name} implements FilterInterface
{{
    public function before(RequestInterface $request, $arguments = null)
    {{
        if (! session()->get('isLoggedIn')) {{
            return redirect()->to(base_url('login'));
        }}
    }}

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {{
        // No after action required.
    }}
}}
''', encoding='utf-8')

# 7) Update footer branding
footer = root / 'app/Views/layout/footer.php'
footer_text = footer.read_text(encoding='utf-8')
footer_text = footer_text.replace('<a href="#" class="footer-brand">SIMONSTER</a>', '<a href="#" class="footer-brand">BATOM</a>')
footer_text = footer_text.replace('&copy; 2026 SIMONSTER Custom Studio. Registered hand-painted wearable art specs protected globally.', '&copy; 2026 BATOM Custom Studio. Registered hand-painted wearable art specs protected globally.')
footer.write_text(footer_text, encoding='utf-8')

print('Patches applied successfully.')
