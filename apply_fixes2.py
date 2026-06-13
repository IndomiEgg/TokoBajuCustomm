from pathlib import Path

root = Path('c:/xampp/htdocs/TokoBajuCustom')

# Safety helper

def write(path: Path, content: str):
    path.write_text(content, encoding='utf-8')

# 1) Patch template.php
layout_path = root / 'app/Views/layout/template.php'
layout = layout_path.read_text(encoding='utf-8')

layout = layout.replace(
    '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">\n    \n    <style>',
    '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">\n    <link rel="stylesheet" href="<?= base_url(\'assets/css/style.css\') ?>">\n    <?= $this->renderSection(\'styles\') ?>'
)

# remove the inline <style> block up to renderSection('styles')
start = layout.find('<style>')
end = layout.find('<?= $this->renderSection(\'styles\') ?>', start)
if start != -1 and end != -1:
    layout = layout[:start] + layout[end:]

layout = layout.replace("<title><?= $title ?? 'SIMONSTER — One of One Wearable Art' ?></title>",
                        "<title><?= $title ?? 'BATOM — One of One Wearable Art' ?></title>")
layout = layout.replace('<div class="loader-logo">SIMONSTER</div>', '<div class="loader-logo">BATOM</div>')
write(layout_path, layout)

# 2) Patch home/index.php
home_path = root / 'app/Views/home/index.php'
home = home_path.read_text(encoding='utf-8')

# replace stylescript section with external script only
scripts_start = home.find('<?= $this->section(\'scripts\') ?>')
scripts_end = home.find('<?= $this->endSection() ?>', scripts_start)
if scripts_start != -1 and scripts_end != -1:
    home = home[:scripts_start] + "<?= $this->section('scripts') ?>\n    <script src=\"<?= base_url('assets/js/home.js') ?>\"></script>\n<?= $this->endSection() ?>" + home[scripts_end + len('<?= $this->endSection() ?>'):]

# patch auth forms to use backend routes and name values
home = home.replace(
    '<form id="auth-signin-form" autocomplete="off">',
    '<form id="auth-signin-form" action="<?= base_url(\'auth/process\') ?>" method="POST" autocomplete="off">'
)
home = home.replace('id="signin-email" class="atelier-input" placeholder=" " required', 'id="signin-email" name="email" class="atelier-input" placeholder=" " required')
home = home.replace('id="signin-password" class="atelier-input" placeholder=" " required', 'id="signin-password" name="password" class="atelier-input" placeholder=" " required')
home = home.replace('id="signin-remember">', 'id="signin-remember" name="remember">')

home = home.replace(
    '<form id="auth-signup-form" autocomplete="off">',
    '<form id="auth-signup-form" action="<?= base_url(\'auth/register\') ?>" method="POST" autocomplete="off">'
)
home = home.replace('id="signup-name" class="atelier-input" placeholder=" " required', 'id="signup-name" name="name" class="atelier-input" placeholder=" " required')
home = home.replace('id="signup-username" class="atelier-input" placeholder=" " required', 'id="signup-username" name="username" class="atelier-input" placeholder=" " required')
home = home.replace('id="signup-email" class="atelier-input" placeholder=" " required', 'id="signup-email" name="email" class="atelier-input" placeholder=" " required')
home = home.replace('id="signup-password" class="atelier-input" placeholder=" " required', 'id="signup-password" name="password" class="atelier-input" placeholder=" " required')
home = home.replace('id="signup-confirm" class="atelier-input" placeholder=" " required', 'id="signup-confirm" name="confirm_password" class="atelier-input" placeholder=" " required')

# branding renames
home = home.replace('SIMONSTER<span>1:1</span>', 'BATOM<span>1:1</span>')
home = home.replace('Simonster stands as a defiant monument against the sterile tide of modern fast fashion and repetitive production loops.', 'Batom stands as a defiant monument against the sterile tide of modern fast fashion and repetitive production loops.')
home = home.replace('<p>Explore daily process reels @simonster.11</p>', '<p>Explore daily process reels @batom.11</p>')
home = home.replace('<span class="aesthetic-card-author">— Head Artisan, SIMONSTER</span>', '<span class="aesthetic-card-author">— Head Artisan, Batom</span>')

write(home_path, home)

# 3) Patch auth/login.php
login_path = root / 'app/Views/auth/login.php'
login = login_path.read_text(encoding='utf-8')
login = login.replace('<title>Login - SIMONSTER</title>', '<title>Login - BATOM</title>')

# remove inline CSS block and add auth.css
link_marker = '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">'
if link_marker in login:
    login = login.replace(link_marker,
                          link_marker + '\n    <link rel="stylesheet" href="<?= base_url(\'assets/css/auth.css\') ?>">')

# remove the inline <style> ... </style> block in the head
style_start = login.find('<style>')
style_end = login.find('</style>', style_start)
head_end = login.find('</head>', style_end if style_end != -1 else 0)
if style_start != -1 and style_end != -1 and head_end != -1:
    login = login[:style_start] + login[style_end + len('</style>'):head_end] + login[head_end:]

# add flash message area after subtitle
flash_html = '''                <?php if(session()->getFlashdata('success')): ?>
                    <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
                <?php endif; ?>
                <?php if(session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
                <?php endif; ?>\n'''
login = login.replace('                <p class="auth-subtitle">Masuk untuk melihat pesanan Anda.</p>\n\n                <div class="form-group">',
                      '                <p class="auth-subtitle">Masuk untuk melihat pesanan Anda.</p>\n' + flash_html + '                <div class="form-group">')

# add form id for auth JS
login = login.replace('<form action="<?= base_url(\'auth/process\') ?>" method="POST" class="auth-form">',
                      '<form id="signin-interactive-form" action="<?= base_url(\'auth/process\') ?>" method="POST" class="auth-form">')

# remove inline script block and add external auth.js
script_start = login.rfind('<script>')
script_end = login.rfind('</script>')
if script_start != -1 and script_end != -1 and script_start < script_end:
    login = login[:script_start] + '    <script src="<?= base_url(\'assets/js/auth.js\') ?>"></script>\n</body>\n</html>'

write(login_path, login)

# 4) Patch Auth controller
auth_path = root / 'app/Controllers/Auth.php'
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
write(auth_path, auth_text)

# 5) Add /auth/register route
routes_path = root / 'app/Config/Routes.php'
routes = routes_path.read_text(encoding='utf-8')
if "$routes->post('/auth/register', 'Auth::register');" not in routes:
    routes = routes.replace("$routes->post('/auth/process', 'Auth::process'); // Validasi login\n",
                            "$routes->post('/auth/process', 'Auth::process'); // Validasi login\n$routes->post('/auth/register', 'Auth::register'); // Register baru\n")
write(routes_path, routes)

# 6) Add filter aliases and files
filters_path = root / 'app/Config/Filters.php'
filters = filters_path.read_text(encoding='utf-8')
if "'authCustomer'  => \\App\\Filters\\AuthCustomer::class" not in filters:
    filters = filters.replace("        'cors'          => Cors::class,\n",
                              "        'cors'          => Cors::class,\n        'authCustomer'  => \\App\\Filters\\AuthCustomer::class,\n        'authAdmin'     => \\App\\Filters\\AuthAdmin::class,\n")
write(filters_path, filters)

filters_dir = root / 'app/Filters'
filters_dir.mkdir(parents=True, exist_ok=True)
for name in ['AuthCustomer.php', 'AuthAdmin.php']:
    file_path = filters_dir / name
    if not file_path.exists():
        class_name = name.replace('.php', '')
        file_path.write_text(f'''<?php

namespace App\\Filters;

use CodeIgniter\\HTTP\\RequestInterface;
use CodeIgniter\\HTTP\\ResponseInterface;
use CodeIgniter\\Filters\\FilterInterface;

class {class_name} implements FilterInterface
{{
    public function before(RequestInterface $request, $arguments = null)
    {{
        if (! session()->get('isLoggedIn'))
        {{
            return redirect()->to(base_url('login'));
        }}
    }}

    public function after(RequestInterface $request, $response, $arguments = null)
    {{
        // No after action required.
    }}
}}
''', encoding='utf-8')

# 7) Rename footer branding
footer_path = root / 'app/Views/layout/footer.php'
footer = footer_path.read_text(encoding='utf-8')
footer = footer.replace('<a href="#" class="footer-brand">SIMONSTER</a>', '<a href="#" class="footer-brand">BATOM</a>')
footer = footer.replace('&copy; 2026 SIMONSTER Custom Studio. Registered hand-painted wearable art specs protected globally.', '&copy; 2026 BATOM Custom Studio. Registered hand-painted wearable art specs protected globally.')
write(footer_path, footer)

# 8) Patch home.js auth submit behavior and branding
home_js_path = root / 'public/assets/js/home.js'
home_js = home_js_path.read_text(encoding='utf-8')

home_js = home_js.replace("showToastNotification('Collector Handshake', 'Welcome back to Simonster. Session verified.');",
                          "showToastNotification('Collector Handshake', 'Welcome back to Batom. Session verified.');")
home_js = home_js.replace("const loginForm = document.getElementById('auth-signin-form');\n            loginForm.addEventListener('submit', (e) => {\n                e.preventDefault();\n                const email = document.getElementById('signin-email').value;",
                          "const loginForm = document.getElementById('auth-signin-form');\n            loginForm.addEventListener('submit', (e) => {\n                e.preventDefault();\n                const email = document.getElementById('signin-email').value;\n")

home_js = home_js.replace(
    "                setTimeout(() => {\n                    btn.style.pointerEvents = 'auto';\n                    btn.style.opacity = '1';\n                    text.textContent = \"Sign In\";\n                    showToastNotification('Collector Handshake', 'Welcome back to Simonster. Session verified.');\n                    loginForm.reset();\n                    switchView('landing');\n                }, 2000);\n            });\n\n            // Register submission loop\n",
    "                setTimeout(() => {\n                    btn.style.pointerEvents = 'auto';\n                    btn.style.opacity = '1';\n                    text.textContent = \"Sign In\";\n                    showToastNotification('Collector Handshake', 'Welcome back to Batom. Session verified.');\n                    loginForm.submit();\n                }, 1200);\n            });\n\n            // Register submission loop\n"
)

home_js = home_js.replace(
    "            registerForm.addEventListener('submit', (e) => {\n                e.preventDefault();\n                const pass = document.getElementById('signup-password').value;\n                const confirm = document.getElementById('signup-confirm').value;\n\n                if (pass !== confirm) {\n                    showToastNotification('Security Warning', 'Blueprints security keys mismatch. Confirm again.', true);\n                    return;\n                }\n\n                const btn = registerForm.querySelector('.btn-submit-commission');\n                const text = btn.querySelector('.btn-text');\n                btn.style.pointerEvents = 'none';\n                btn.style.opacity = '0.7';\n                text.innerHTML = '<i class="fa-solid fa-compass-drafting fa-spin"></i> Registering identity...';\n\n                setTimeout(() => {\n                    btn.style.pointerEvents = 'auto';\n                    btn.style.opacity = '1';\n                    text.textContent = \"Create Collector Profile\";\n                    showToastNotification('Collector Initiated', 'Vault profile established. Switch tabs to authenticate.');\n                    registerForm.reset();\n                    // trigger click tab signin\n                    document.querySelector('.tab-trigger-btn[data-auth-tab="signin"]').click();\n                }, 2200);\n            });\n\n            // Extra buttons\n",
    "            registerForm.addEventListener('submit', (e) => {\n                e.preventDefault();\n                const pass = document.getElementById('signup-password').value;\n                const confirm = document.getElementById('signup-confirm').value;\n\n                if (pass !== confirm) {\n                    showToastNotification('Security Warning', 'Blueprints security keys mismatch. Confirm again.', true);\n                    return;\n                }\n\n                const btn = registerForm.querySelector('.btn-submit-commission');\n                const text = btn.querySelector('.btn-text');\n                btn.style.pointerEvents = 'none';\n                btn.style.opacity = '0.7';\n                text.innerHTML = '<i class="fa-solid fa-compass-drafting fa-spin"></i> Registering identity...';\n\n                setTimeout(() => {\n                    btn.style.pointerEvents = 'auto';\n                    btn.style.opacity = '1';\n                    text.textContent = \"Create Collector Profile\";\n                    showToastNotification('Collector Initiated', 'Vault profile established. Switch tabs to authenticate.');\n                    registerForm.submit();\n                }, 1400);\n            });\n\n            // Extra buttons\n"
)

write(home_js_path, home_js)

print('apply_fixes2 completed successfully.')
