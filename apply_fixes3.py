from pathlib import Path

root = Path('c:/xampp/htdocs/TokoBajuCustom')

# Utility helpers

def write(path: Path, text: str):
    path.write_text(text, encoding='utf-8')


def remove_style_block(text: str, marker: str):
    start = text.find(marker)
    if start == -1:
        return text
    style_start = text.find('<style>', start)
    if style_start == -1:
        return text
    style_end = text.find('</style>', style_start)
    if style_end == -1:
        return text
    style_end += len('</style>')
    return text[:style_start] + text[style_end:]


def remove_script_section(text: str, start_marker: str, end_marker: str):
    start = text.find(start_marker)
    if start == -1:
        return text
    end = text.find(end_marker, start)
    if end == -1:
        return text
    end += len(end_marker)
    return text[:start] + text[end:]

# -----------------------------------------------------------------------------
# 1) template.php: remove inline CSS and include style.css
# -----------------------------------------------------------------------------
layout_path = root / 'app/Views/layout/template.php'
layout = layout_path.read_text(encoding='utf-8')
layout = layout.replace(
    '<title><?= $title ?? \'SIMONSTER — One of One Wearable Art\' ?></title>',
    '<title><?= $title ?? \'BATOM — One of One Wearable Art\' ?></title>'
)
layout = layout.replace(
    '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">',
    '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">\n    <link rel="stylesheet" href="<?= base_url(\'assets/css/style.css\') ?>">\n    <?= $this->renderSection(\'styles\') ?>'
)
layout = remove_style_block(layout, '<?= $this->renderSection(\'styles\') ?>')
layout = layout.replace('<div class="loader-logo">SIMONSTER</div>', '<div class="loader-logo">BATOM</div>')
write(layout_path, layout)

# -----------------------------------------------------------------------------
# 2) auth/login.php: remove inline CSS/JS and use auth assets
# -----------------------------------------------------------------------------
login_path = root / 'app/Views/auth/login.php'
login = login_path.read_text(encoding='utf-8')
login = login.replace('<title>Login - SIMONSTER</title>', '<title>Login - BATOM</title>')
login = login.replace(
    '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">',
    '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">\n    <link rel="stylesheet" href="<?= base_url(\'assets/css/auth.css\') ?>">'
)
login = remove_style_block(login, '<link rel="stylesheet" href="<?= base_url(\'assets/css/auth.css\') ?>">')
login = login.replace('<form action="<?= base_url(\'auth/process\') ?>" method="POST" class="auth-form">',
                      '<form id="signin-interactive-form" action="<?= base_url(\'auth/process\') ?>" method="POST" class="auth-form">')
# flash messages
flash_html = '''                <?php if(session()->getFlashdata('success')): ?>\n                    <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>\n                <?php endif; ?>\n                <?php if(session()->getFlashdata('error')): ?>\n                    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>\n                <?php endif; ?>\n'''
login = login.replace('                <p class="auth-subtitle">Masuk untuk melihat pesanan Anda.</p>\n\n                <div class="form-group">',
                      '                <p class="auth-subtitle">Masuk untuk melihat pesanan Anda.</p>\n' + flash_html + '                <div class="form-group">')
# remove inline script block
login = remove_script_section(login, '<script>', '</script>')
# add external auth.js before body close
if '    <script src="<?= base_url(\'assets/js/auth.js\') ?>"></script>' not in login:
    login = login.replace('</body>', '    <script src="<?= base_url(\'assets/js/auth.js\') ?>"></script>\n</body>')
write(login_path, login)

# -----------------------------------------------------------------------------
# 3) home/index.php: externalise script section and wire auth forms
# -----------------------------------------------------------------------------
home_path = root / 'app/Views/home/index.php'
home = home_path.read_text(encoding='utf-8')
home = home.replace('SIMONSTER<span>1:1</span>', 'BATOM<span>1:1</span>')
home = home.replace('Simonster stands as a defiant monument against the sterile tide of modern fast fashion and repetitive production loops.', 'Batom stands as a defiant monument against the sterile tide of modern fast fashion and repetitive production loops.')
home = home.replace('<p>Explore daily process reels @simonster.11</p>', '<p>Explore daily process reels @batom.11</p>')
home = home.replace('<span class="aesthetic-card-author">— Head Artisan, SIMONSTER</span>', '<span class="aesthetic-card-author">— Head Artisan, Batom</span>')
home = home.replace('<form id="auth-signin-form" autocomplete="off">', '<form id="auth-signin-form" action="<?= base_url(\'auth/process\') ?>" method="POST" autocomplete="off">')
home = home.replace('id="signin-email" class="atelier-input" placeholder=" " required', 'id="signin-email" name="email" class="atelier-input" placeholder=" " required')
home = home.replace('id="signin-password" class="atelier-input" placeholder=" " required', 'id="signin-password" name="password" class="atelier-input" placeholder=" " required')
home = home.replace('id="signin-remember">', 'id="signin-remember" name="remember">')
home = home.replace('<form id="auth-signup-form" autocomplete="off">', '<form id="auth-signup-form" action="<?= base_url(\'auth/register\') ?>" method="POST" autocomplete="off">')
home = home.replace('id="signup-name" class="atelier-input" placeholder=" " required', 'id="signup-name" name="name" class="atelier-input" placeholder=" " required')
home = home.replace('id="signup-username" class="atelier-input" placeholder=" " required', 'id="signup-username" name="username" class="atelier-input" placeholder=" " required')
home = home.replace('id="signup-email" class="atelier-input" placeholder=" " required', 'id="signup-email" name="email" class="atelier-input" placeholder=" " required')
home = home.replace('id="signup-password" class="atelier-input" placeholder=" " required', 'id="signup-password" name="password" class="atelier-input" placeholder=" " required')
home = home.replace('id="signup-confirm" class="atelier-input" placeholder=" " required', 'id="signup-confirm" name="confirm_password" class="atelier-input" placeholder=" " required')
start_marker = '<?= $this->section(\'scripts\') ?>'
end_marker = '<?= $this->endSection() ?>'
start = home.find(start_marker)
if start != -1:
    end = home.find(end_marker, start)
    if end != -1:
        end += len(end_marker)
        home = home[:start] + "<?= $this->section('scripts') ?>\n    <script src=\"<?= base_url('assets/js/home.js') ?>\"></script>\n<?= $this->endSection() ?>" + home[end:]
write(home_path, home)

# -----------------------------------------------------------------------------
# 4) Auth controller
# -----------------------------------------------------------------------------
auth_path = root / 'app/Controllers/Auth.php'
auth_text = r'''<?php

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

# -----------------------------------------------------------------------------
# 5) Routes
# -----------------------------------------------------------------------------
routes_path = root / 'app/Config/Routes.php'
routes = routes_path.read_text(encoding='utf-8')
if "$routes->post('/auth/register', 'Auth::register');" not in routes:
    routes = routes.replace("$routes->post('/auth/process', 'Auth::process'); // Validasi login\n",
                            "$routes->post('/auth/process', 'Auth::process'); // Validasi login\n$routes->post('/auth/register', 'Auth::register'); // Register baru\n")
write(routes_path, routes)

# -----------------------------------------------------------------------------
# 6) Filters
# -----------------------------------------------------------------------------
filters_path = root / 'app/Config/Filters.php'
filters = filters_path.read_text(encoding='utf-8')
if "'authCustomer'  => \App\Filters\AuthCustomer::class" not in filters:
    filters = filters.replace("        'cors'          => Cors::class,\n",
                              "        'cors'          => Cors::class,\n        'authCustomer'  => \App\Filters\AuthCustomer::class,\n        'authAdmin'     => \App\Filters\AuthAdmin::class,\n")
write(filters_path, filters)

filters_dir = root / 'app/Filters'
filters_dir.mkdir(parents=True, exist_ok=True)
for name in ['AuthCustomer.php', 'AuthAdmin.php']:
    file_path = filters_dir / name
    if not file_path.exists():
        class_name = name.replace('.php', '')
        file_path.write_text(r'''<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class %s implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (! session()->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No after action required.
    }
}
''' % class_name, encoding='utf-8')

print('apply_fixes3 completed successfully.')
