from pathlib import Path

root = Path('c:/xampp/htdocs/TokoBajuCustom')

# Helper to replace the first occurrence of a substring and raise if missing

def replace_once(text, old, new, filename):
    if old not in text:
        raise ValueError(f"Pattern not found in {filename}: {old[:80]!r}")
    return text.replace(old, new, 1)

# 1) Update layout template: external CSS and brand rename
layout = root / 'app/Views/layout/template.php'
text = layout.read_text(encoding='utf-8')
text = replace_once(
    text,
    '    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">\n    \n    <style>',
    '    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">\n    <link rel="stylesheet" href="<?= base_url(\'assets/css/style.css\') ?>">',
    layout
)
text = text.replace("<title><?= $title ?? 'SIMONSTER — One of One Wearable Art' ?></title>", "<title><?= $title ?? 'BATOM — One of One Wearable Art' ?></title>")
text = text.replace('<div class="loader-logo">SIMONSTER</div>', '<div class="loader-logo">BATOM</div>')
text = text.replace('    </style>\n\n    <?= $this->renderSection(\'styles\') ?>', '    <?= $this->renderSection(\'styles\') ?>')
layout.write_text(text, encoding='utf-8')

# 2) Update home view. Only wire external script and BATOM branding in home view.
home = root / 'app/Views/home/index.php'
text = home.read_text(encoding='utf-8')
text = text.replace('<?= $this->section(\'scripts\') ?>\n    <script>', "<?= $this->section('scripts') ?>\n    <script src=\"<?= base_url('assets/js/home.js') ?>\"></script>\n")
text = text.replace("showToastNotification('Collector Handshake', 'Welcome back to Simonster. Session verified.');", "showToastNotification('Collector Handshake', 'Welcome back to Batom. Session verified.');")
text = text.replace('                                <span class="aesthetic-card-author">— Head Artisan, SIMONSTER</span>', '                                <span class="aesthetic-card-author">— Head Artisan, Batom</span>')
text = text.replace('                                Simonster stands as a defiant monument against the sterile tide of modern fast fashion and repetitive production loops.', '                                Batom stands as a defiant monument against the sterile tide of modern fast fashion and repetitive production loops.')
text = text.replace('                                Every single paint splatter, complex embroidery trace, and raw-edge cut is processed manually inside our Jakarta and Tokyo-based workshop cells. When you wear a Simonster original, you are carrying a piece of a living museum.', '                                Every single paint splatter, complex embroidery trace, and raw-edge cut is processed manually inside our Jakarta and Tokyo-based workshop cells. When you wear a Batom original, you are carrying a piece of a living museum.')
text = text.replace('                                    <p>Explore daily process reels @simonster.11</p>', '                                    <p>Explore daily process reels @batom.11</p>')
text = text.replace('                    <h2 class="logo" style="font-size: 3.5rem; letter-spacing: 0.3em; margin-bottom: 20px;">SIMONSTER<span>1:1</span></h2>', '                    <h2 class="logo" style="font-size: 3.5rem; letter-spacing: 0.3em; margin-bottom: 20px;">BATOM<span>1:1</span></h2>')
# If there are any remaining SIMONSTER/Simonster in the home view, preserve them for later manual review.
home.write_text(text, encoding='utf-8')

# 3) Update footer branding
footer = root / 'app/Views/layout/footer.php'
text = footer.read_text(encoding='utf-8')
text = text.replace('<a href="#" class="footer-brand">SIMONSTER</a>', '<a href="#" class="footer-brand">BATOM</a>')
text = text.replace('&copy; 2026 SIMONSTER Custom Studio. Registered hand-painted wearable art specs protected globally.', '&copy; 2026 BATOM Custom Studio. Registered hand-painted wearable art specs protected globally.')
footer.write_text(text, encoding='utf-8')

# 4) Update auth login view to use external assets and add flash notification display
login = root / 'app/Views/auth/login.php'
text = login.read_text(encoding='utf-8')
text = text.replace('<title>Login - SIMONSTER</title>', '<title>Login - BATOM</title>')
text = text.replace(
    '    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">\n    \n    <style>',
    '    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">\n    <link rel="stylesheet" href="<?= base_url(\'assets/css/auth.css\') ?>">\n'
)
text = text.replace('    </style>\n</head>', '</head>')
text = text.replace('            <button type="submit" class="btn-auth-submit">Enter Portal</button>', '            <button type="submit" class="btn-auth-submit"><span class="btn-text">Enter Portal</span></button>')
# Add flash message container after subtitle
flash_html = '''                <?php if(session()->getFlashdata('success')): ?>
                    <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
                <?php endif; ?>
                <?php if(session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
                <?php endif; ?>
'''
text = text.replace('                <p class="auth-subtitle">Masuk untuk melihat pesanan Anda.</p>\n\n                <div class="form-group">', '                <p class="auth-subtitle">Masuk untuk melihat pesanan Anda.</p>\n' + flash_html + '\n                <div class="form-group">')
# Replace entire inline script block with single external script tag
script_start = text.find('    <script>')
script_end = text.rfind('</script>')
if script_start != -1 and script_end != -1 and script_end > script_start:
    text = text[:script_start] + '    <script src="<?= base_url(\'assets/js/auth.js\') ?>"></script>\n</body>\n</html>'
login.write_text(text, encoding='utf-8')

# 5) Wire DB auth controller and auth register route
auth = root / 'app/Controllers/Auth.php'
text = auth.read_text(encoding='utf-8')
new_auth = '''<?php

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
            'userId'     => $user['id'],
            'userName'   => $user['name'],
            'userEmail'  => $user['email'],
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
            'name'     => $name,
            'username' => $username,
            'email'    => $email,
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
# Replace file contents entirely
auth.write_text(new_auth, encoding='utf-8')

# Add register route if missing
routes = root / 'app/Config/Routes.php'
text = routes.read_text(encoding='utf-8')
if "$routes->post('/auth/register', 'Auth::register');" not in text:
    text = text.replace("$routes->post('/auth/process', 'Auth::process'); // Validasi login\n", "$routes->post('/auth/process', 'Auth::process'); // Validasi login\n$routes->post('/auth/register', 'Auth::register'); // Register baru\n")
routes.write_text(text, encoding='utf-8')

# 6) Create auth filters if needed
filters_php = root / 'app/Config/Filters.php'
text = filters_php.read_text(encoding='utf-8')
if "'authCustomer'  => \\App\\Filters\\AuthCustomer::class" not in text:
    text = text.replace("        'cors'          => Cors::class,\n", "        'cors'          => Cors::class,\n        'authCustomer'  => \\App\\Filters\\AuthCustomer::class,\n        'authAdmin'     => \\App\\Filters\\AuthAdmin::class,\n")
filters_php.write_text(text, encoding='utf-8')

filters_path = root / 'app/Filters'
filters_path.mkdir(parents=True, exist_ok=True)
customer = filters_path / 'AuthCustomer.php'
admin = filters_path / 'AuthAdmin.php'
if not customer.exists():
    customer.write_text('''<?php

namespace App\\Filters;

use CodeIgniter\\HTTP\\RequestInterface;
use CodeIgniter\\HTTP\\ResponseInterface;
use CodeIgniter\\Filters\\FilterInterface;

class AuthCustomer implements FilterInterface
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
''', encoding='utf-8')
if not admin.exists():
    admin.write_text('''<?php

namespace App\\Filters;

use CodeIgniter\\HTTP\\RequestInterface;
use CodeIgniter\\HTTP\\ResponseInterface;
use CodeIgniter\\Filters\\FilterInterface;

class AuthAdmin implements FilterInterface
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
''', encoding='utf-8')

# 7) Update public assets JS for static URLs and Batom text
home_js = root / 'public/assets/js/home.js'
text = home_js.read_text(encoding='utf-8')
text = text.replace('window.location.href = "<?= base_url(\'shop/custom\') ?>";', 'window.location.href = "/shop/custom";')
text = text.replace('window.location.href = "<?= base_url(\'cart\') ?>";', 'window.location.href = "/cart";')
text = text.replace("showToastNotification('Collector Handshake', 'Welcome back to Simonster. Session verified.');", "showToastNotification('Collector Handshake', 'Welcome back to Batom. Session verified.');")
text = text.replace("?text=SIMONSTER'", "?text=BATOM'")
# auth text not relevant, but can include if any
home_js.write_text(text, encoding='utf-8')

# 8) Update auth JS to allow actual form submission
auth_js = root / 'public/assets/js/auth.js'
text = auth_js.read_text(encoding='utf-8')
text = text.replace(
    "            if (signinForm) {\n                signinForm.addEventListener('submit', (e) => {\n                    e.preventDefault();\n                    const email = document.getElementById('signin-email').value;\n                    showToastNotification('Cryptography handshake', 'Verifying cryptographic security keys for: ' + email);\n\n                    const btn = loginForm.querySelector('.btn-submit-commission');\n                    const text = btn.querySelector('.btn-text');\n                    btn.style.pointerEvents = 'none';\n                    btn.style.opacity = '0.7';\n                    text.innerHTML = '<i class="fa-solid fa-compass-drafting fa-spin"></i> Authenticating...';\n\n                    setTimeout(() => {\n                        btn.style.pointerEvents = 'auto';\n                        btn.style.opacity = '1';\n                        text.textContent = "Sign In";\n                        showToastNotification('Collector Handshake', 'Welcome back to Simonster. Session verified.');\n                        loginForm.reset();\n                        switchView('landing');\n                    }, 2000);\n                });\n            }\n\n            // Register submission loop\n            const registerForm = document.getElementById('auth-signup-form');\n            registerForm.addEventListener('submit', (e) => {\n                e.preventDefault();\n                const pass = document.getElementById('signup-password').value;\n                const confirm = document.getElementById('signup-confirm').value;\n\n                if (pass !== confirm) {\n                    showToastNotification('Security Warning', 'Blueprints security keys mismatch. Confirm again.', true);\n                    return;\n                }\n\n                const btn = registerForm.querySelector('.btn-submit-commission');\n                const text = btn.querySelector('.btn-text');\n                btn.style.pointerEvents = 'none';\n                btn.style.opacity = '0.7';\n                text.innerHTML = '<i class="fa-solid fa-compass-drafting fa-spin"></i> Registering identity...';\n\n                setTimeout(() => {\n                    btn.style.pointerEvents = 'auto';\n                    btn.style.opacity = '1';\n                    text.textContent = "Create Collector Profile";\n                    showToastNotification('Collector Initiated', 'Vault profile established. Switch tabs to authenticate.');\n                    registerForm.reset();\n                    // trigger click tab signin\n                    document.querySelector('.tab-trigger-btn[data-auth-tab="signin"]').click();\n                }, 2200);\n            });\n\n            // Extra buttons\n",
    "            if (signinForm) {\n                signinForm.addEventListener('submit', () => {\n                    const email = document.getElementById('signin-email').value;\n                    showToastNotification('Cryptography handshake', 'Verifying cryptographic security keys for: ' + email);\n\n                    const btn = signinForm.querySelector('.btn-auth-submit');\n                    const text = btn.querySelector('.btn-text');\n                    if (text) {\n                        btn.style.pointerEvents = 'none';\n                        btn.style.opacity = '0.7';\n                        text.innerHTML = '<i class=\"fa-solid fa-compass-drafting fa-spin\"></i> Authenticating...';\n                    }\n                });\n            }\n\n            const registerForm = document.getElementById('signup-interactive-form');\n            if (registerForm) {\n                registerForm.addEventListener('submit', (e) => {\n                    const pass = document.getElementById('signup-password').value;\n                    const confirm = document.getElementById('signup-confirm').value;\n                    if (pass !== confirm) {\n                        e.preventDefault();\n                        showToastNotification('MISMATCH WARNING', 'Design key passwords do not match. Review confirm parameters.', true);\n                    }\n                });\n            }\n\n            // Extra buttons\n"
)
auth_js.write_text(text, encoding='utf-8')

# 9) Add minimal auth DB env sample for local XAMPP
env = root / '.env'
text = env.read_text(encoding='utf-8')
if 'database.default.database = batom' not in text:
    text += '\n# Local database settings for Batom login/sign-up\ndatabase.default.hostname = localhost\ndatabase.default.database = batom\ndatabase.default.username = root\ndatabase.default.password = \ndatabase.default.DBDriver = MySQLi\n'
env.write_text(text, encoding='utf-8')

print('Fixes applied')
