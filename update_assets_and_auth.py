from pathlib import Path

root = Path('c:/xampp/htdocs/TokoBajuCustom')

replacements = []

# 1) Layout template: remove inline CSS, add asset link, rename brand texts
layout = root / 'app/Views/layout/template.php'
text = layout.read_text(encoding='utf-8')
old = '''    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
'''
replacement = '''    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
'''
text = text.replace(old, replacement)
text = text.replace("<title><?= $title ?? 'SIMONSTER — One of One Wearable Art' ?></title>", "<title><?= $title ?? 'BATOM — One of One Wearable Art' ?></title>")
text = text.replace('<div class="loader-logo">SIMONSTER</div>', '<div class="loader-logo">BATOM</div>')
# Remove closing </style> before renderSection('styles')
text = text.replace('    </style>\n\n    <?= $this->renderSection(\'styles\') ?>', '')
layout.write_text(text, encoding='utf-8')

# 2) Home page: Replace inline script section with external JS and wire auth forms & branding
home = root / 'app/Views/home/index.php'
text = home.read_text(encoding='utf-8')
text = text.replace('            <form id="auth-signin-form" autocomplete="off">', '            <form id="auth-signin-form" action="<?= base_url(\'auth/process\') ?>" method="POST" autocomplete="off">')
text = text.replace('                                    <input type="email" id="signin-email" class="atelier-input" placeholder=" " required>', '                                    <input type="email" id="signin-email" name="email" class="atelier-input" placeholder=" " required>')
text = text.replace('                                    <input type="password" id="signin-password" class="atelier-input" placeholder=" " required>', '                                    <input type="password" id="signin-password" name="password" class="atelier-input" placeholder=" " required>')
text = text.replace('            <form id="auth-signup-form" autocomplete="off">', '            <form id="auth-signup-form" action="<?= base_url(\'auth/register\') ?>" method="POST" autocomplete="off">')
text = text.replace('                                        <input type="text" id="signup-name" class="atelier-input" placeholder=" " required>', '                                        <input type="text" id="signup-name" name="name" class="atelier-input" placeholder=" " required>')
text = text.replace('                                        <input type="text" id="signup-username" class="atelier-input" placeholder=" " required>', '                                        <input type="text" id="signup-username" name="username" class="atelier-input" placeholder=" " required>')
text = text.replace('                                    <input type="email" id="signup-email" class="atelier-input" placeholder=" " required>', '                                    <input type="email" id="signup-email" name="email" class="atelier-input" placeholder=" " required>')
text = text.replace('                                        <input type="password" id="signup-password" class="atelier-input" placeholder=" " required>', '                                        <input type="password" id="signup-password" name="password" class="atelier-input" placeholder=" " required>')
text = text.replace('                                        <input type="password" id="signup-confirm" class="atelier-input" placeholder=" " required>', '                                        <input type="password" id="signup-confirm" name="confirm_password" class="atelier-input" placeholder=" " required>')
text = text.replace('                                <span class="aesthetic-card-author">— Head Artisan, SIMONSTER</span>', '                                <span class="aesthetic-card-author">— Head Artisan, Batom</span>')
text = text.replace('                                Simonster stands as a defiant monument against the sterile tide of modern fast fashion and repetitive production loops.', '                                Batom stands as a defiant monument against the sterile tide of modern fast fashion and repetitive production loops.')
text = text.replace('                                Every single paint splatter, complex embroidery trace, and raw-edge cut is processed manually inside our Jakarta and Tokyo-based workshop cells. When you wear a Simonster original, you are carrying a piece of a living museum.', '                                Every single paint splatter, complex embroidery trace, and raw-edge cut is processed manually inside our Jakarta and Tokyo-based workshop cells. When you wear a Batom original, you are carrying a piece of a living museum.')
text = text.replace('                                    <p>Explore daily process reels @simonster.11</p>', '                                    <p>Explore daily process reels @batom.11</p>')
text = text.replace('                    <h2 class="logo" style="font-size: 3.5rem; letter-spacing: 0.3em; margin-bottom: 20px;">SIMONSTER<span>1:1</span></h2>', '                    <h2 class="logo" style="font-size: 3.5rem; letter-spacing: 0.3em; margin-bottom: 20px;">BATOM<span>1:1</span></h2>')
text = text.replace("                    showToastNotification('Collector Handshake', 'Welcome back to Simonster. Session verified.');", "                    showToastNotification('Collector Handshake', 'Welcome back to Batom. Session verified.');")
old_script_section = text[text.rfind('<?= $this->section(\'scripts\') ?>'):]
if '<script>' in old_script_section and '</script>' in old_script_section:
    start = text.rfind('<?= $this->section(\'scripts\') ?>')
    end = text.rfind('<?= $this->endSection() ?>') + len('<?= $this->endSection() ?>')
    text = text[:start] + "<?= $this->section('scripts') ?>\n    <script src=\"<?= base_url('assets/js/home.js') ?>\"></script>\n<?= $this->endSection() ?>\n" + text[end:]
home.write_text(text, encoding='utf-8')

# 3) Footer brand/content rename
footer = root / 'app/Views/layout/footer.php'
text = footer.read_text(encoding='utf-8')
text = text.replace('<a href="#" class="footer-brand">SIMONSTER</a>', '<a href="#" class="footer-brand">BATOM</a>')
text = text.replace('&copy; 2026 SIMONSTER Custom Studio. Registered hand-painted wearable art specs protected globally.', '&copy; 2026 BATOM Custom Studio. Registered hand-painted wearable art specs protected globally.')
footer.write_text(text, encoding='utf-8')

# 4) Auth login page: externalize assets and rename title
login = root / 'app/Views/auth/login.php'
text = login.read_text(encoding='utf-8')
text = text.replace('<title>Login - SIMONSTER</title>', '<title>Login - Batom</title>')
text = text.replace('<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">\n    \n    <style>', '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">\n    <link rel="stylesheet" href="<?= base_url(\'assets/css/auth.css\') ?>">\n')
text = text.replace('    </style>\n</head>', '</head>')
if '<script>' in text and '</script>' in text:
    start = text.rfind('<script>')
    end = text.rfind('</script>') + len('</script>')
    text = text[:start] + '    <script src="<?= base_url(\'assets/js/auth.js\') ?>"></script>\n</body>\n</html>'
login.write_text(text, encoding='utf-8')

# 5) Update Auth controller to use database auth
auth = root / 'app/Controllers/Auth.php'
text = auth.read_text(encoding='utf-8')
text = text.replace('class Auth extends BaseController\n{\n    public function login()\n    {\n        return view(\'auth/login\');\n    }\n\n    public function process()\n    {\n        $session = session();\n        $email = $this->request->getPost(\'email\');\n        $session->setFlashdata(\'auth_status\', \'Login simulated for \' . esc($email));\n        return redirect()->to(base_url('/login'));\n    }\n\n    public function logout()\n    {\n        session()->destroy();\n        return redirect()->to(base_url('/'));\n    }\n}\n', "use App\\Models\\UserModel;\n\nclass Auth extends BaseController\n{\n    public function login()\n    {\n        return view('auth/login');\n    }\n\n    public function process()\n    {\n        $session = session();\n        $email = $this->request->getPost('email');\n        $password = $this->request->getPost('password');\n\n        $userModel = new UserModel();\n        $user = $userModel->findByEmail($email);\n\n        if (! $user || ! password_verify($password, $user['password'])) {\n            $session->setFlashdata('error', 'Invalid email or password. Please try again.');\n            return redirect()->back()->withInput();\n        }\n\n        $session->set([\n            'isLoggedIn' => true,\n            'userId'     => $user['id'],\n            'userName'   => $user['name'],\n            'userEmail'  => $user['email'],\n        ]);\n\n        $session->setFlashdata('success', 'Welcome back, ' . esc($user['name']) . '.');\n        return redirect()->to(base_url('/'));\n    }\n\n    public function register()\n    {\n        $session = session();\n        $name = $this->request->getPost('name');\n        $username = $this->request->getPost('username');\n        $email = $this->request->getPost('email');\n        $password = $this->request->getPost('password');\n        $confirm = $this->request->getPost('confirm_password');\n\n        if ($password !== $confirm) {\n            $session->setFlashdata('error', 'Passwords do not match. Please confirm again.');\n            return redirect()->back()->withInput();\n        }\n\n        $userModel = new UserModel();\n        if ($userModel->findByEmail($email)) {\n            $session->setFlashdata('error', 'An account with that email already exists.');\n            return redirect()->back()->withInput();\n        }\n\n        $userModel->save([\n            'name'     => $name,\n            'username' => $username,\n            'email'    => $email,\n            'password' => password_hash($password, PASSWORD_DEFAULT),\n        ]);\n\n        $session->setFlashdata('success', 'Account created successfully. Please sign in.');\n        return redirect()->to(base_url('/login'));\n    }\n\n    public function logout()\n    {\n        session()->destroy();\n        return redirect()->to(base_url('/'));\n    }\n}\n")
auth.write_text(text, encoding='utf-8')

# 6) Add auth register route
routes = root / 'app/Config/Routes.php'
text = routes.read_text(encoding='utf-8')
if "post('/auth/register', 'Auth::register');" not in text:
    text = text.replace("$routes->post('/auth/process', 'Auth::process'); // Validasi login\n", "$routes->post('/auth/process', 'Auth::process'); // Validasi login\n$routes->post('/auth/register', 'Auth::register'); // Register baru\n")
routes.write_text(text, encoding='utf-8')

# 7) Add filter aliases and filter classes if missing
filters = root / 'app/Config/Filters.php'
text = filters.read_text(encoding='utf-8')
if "'authCustomer' => AuthCustomer::class," not in text:
    text = text.replace("        'cors'          => Cors::class,\n", "        'cors'          => Cors::class,\n        'authCustomer'  => \App\\Filters\\AuthCustomer::class,\n        'authAdmin'     => \App\\Filters\\AuthAdmin::class,\n")
filters.write_text(text, encoding='utf-8')

# Create filter class files if they don't exist
filters_path = root / 'app/Filters'
customer = filters_path / 'AuthCustomer.php'
admin = filters_path / 'AuthAdmin.php'
if not customer.exists():
    customer.write_text("<?php\n\nnamespace App\\Filters;\n\nuse CodeIgniter\\HTTP\\RequestInterface;\nuse CodeIgniter\\HTTP\\ResponseInterface;\nuse CodeIgniter\\Filters\\FilterInterface;\n\nclass AuthCustomer implements FilterInterface\n{\n    public function before(RequestInterface $request, $arguments = null)\n    {\n        if (! session()->get('isLoggedIn')) {\n            return redirect()->to(base_url('login'));\n        }\n    }\n\n    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)\n    {\n        // No after action required.\n    }\n}\n", encoding='utf-8')
if not admin.exists():
    admin.write_text("<?php\n\nnamespace App\\Filters;\n\nuse CodeIgniter\\HTTP\\RequestInterface;\nuse CodeIgniter\\HTTP\\ResponseInterface;\nuse CodeIgniter\\Filters\\FilterInterface;\n\nclass AuthAdmin implements FilterInterface\n{\n    public function before(RequestInterface $request, $arguments = null)\n    {\n        if (! session()->get('isLoggedIn')) {\n            return redirect()->to(base_url('login'));\n        }\n    }\n\n    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)\n    {\n        // No after action required.\n    }\n}\n", encoding='utf-8')

# 8) Update public home JS dynamics and auth form handling
home_js = root / 'public/assets/js/home.js'
text = home_js.read_text(encoding='utf-8')
text = text.replace('window.location.href = "<?= base_url(\'shop/custom\') ?>";', 'window.location.href = "/shop/custom";')
text = text.replace('window.location.href = "<?= base_url(\'cart\') ?>";', 'window.location.href = "/cart";')
text = text.replace("showToastNotification('Collector Handshake', 'Welcome back to Simonster. Session verified.');", "showToastNotification('Collector Handshake', 'Welcome back to Batom. Session verified.');")
text = text.replace("onerror=\'this.src='https://placehold.co/300x400/080808/8b0000?text=SIMONSTER'\"", "onerror=\'this.src='https://placehold.co/300x400/080808/8b0000?text=BATOM'\"")
text = text.replace("onerror=\'this.src='https://placehold.co/300x400/080808/8b0000?text=SIMONSTER'\"", "onerror=\'this.src='https://placehold.co/300x400/080808/8b0000?text=BATOM'\"")
# Update auth form submit handlers and guards
text = text.replace("            const loginForm = document.getElementById('auth-signin-form');\n            loginForm.addEventListener('submit', (e) => {\n                e.preventDefault();\n                const email = document.getElementById('signin-email').value;\n                showToastNotification('Cryptography handshake', 'Verifying cryptographic security keys for: ' + email);\n\n                const btn = loginForm.querySelector('.btn-submit-commission');\n                const text = btn.querySelector('.btn-text');\n                btn.style.pointerEvents = 'none';\n                btn.style.opacity = '0.7';\n                text.innerHTML = '<i class="fa-solid fa-compass-drafting fa-spin"></i> Authenticating...';\n\n                setTimeout(() => {\n                    btn.style.pointerEvents = 'auto';\n                    btn.style.opacity = '1';\n                    text.textContent = "Sign In";\n                    showToastNotification('Collector Handshake', 'Welcome back to Simonster. Session verified.');\n                    loginForm.reset();\n                    switchView('landing');\n                }, 2000);\n            });\n\n            // Register submission loop\n            const registerForm = document.getElementById('auth-signup-form');\n            registerForm.addEventListener('submit', (e) => {\n                e.preventDefault();\n                const pass = document.getElementById('signup-password').value;\n                const confirm = document.getElementById('signup-confirm').value;\n\n                if (pass !== confirm) {\n                    showToastNotification('Security Warning', 'Blueprints security keys mismatch. Confirm again.', true);\n                    return;\n                }\n\n                const btn = registerForm.querySelector('.btn-submit-commission');\n                const text = btn.querySelector('.btn-text');\n                btn.style.pointerEvents = 'none';\n                btn.style.opacity = '0.7';\n                text.innerHTML = '<i class="fa-solid fa-compass-drafting fa-spin"></i> Registering identity...';\n\n                setTimeout(() => {\n                    btn.style.pointerEvents = 'auto';\n                    btn.style.opacity = '1';\n                    text.textContent = "Create Collector Profile";\n                    showToastNotification('Collector Initiated', 'Vault profile established. Switch tabs to authenticate.');\n                    registerForm.reset();\n                    // trigger click tab signin\n                    document.querySelector('.tab-trigger-btn[data-auth-tab="signin"]').click();\n                }, 2200);\n            });\n\n            // Extra buttons\n", "            const loginForm = document.getElementById('auth-signin-form');\n            if (loginForm) {\n                loginForm.addEventListener('submit', () => {\n                    const email = document.getElementById('signin-email').value;\n                    showToastNotification('Cryptography handshake', 'Verifying cryptographic security keys for: ' + email);\n\n                    const btn = loginForm.querySelector('.btn-submit-commission');\n                    const text = btn.querySelector('.btn-text');\n                    btn.style.pointerEvents = 'none';\n                    btn.style.opacity = '0.7';\n                    text.innerHTML = '<i class="fa-solid fa-compass-drafting fa-spin"></i> Authenticating...';\n                });\n            }\n\n            const registerForm = document.getElementById('auth-signup-form');\n            if (registerForm) {\n                registerForm.addEventListener('submit', (e) => {\n                    const pass = document.getElementById('signup-password').value;\n                    const confirm = document.getElementById('signup-confirm').value;\n\n                    if (pass !== confirm) {\n                        e.preventDefault();\n                        showToastNotification('Security Warning', 'Blueprints security keys mismatch. Confirm again.', true);\n                        return;\n                    }\n\n                    const btn = registerForm.querySelector('.btn-submit-commission');\n                    const text = btn.querySelector('.btn-text');\n                    btn.style.pointerEvents = 'none';\n                    btn.style.opacity = '0.7';\n                    text.innerHTML = '<i class="fa-solid fa-compass-drafting fa-spin"></i> Registering identity...';\n                });\n            }\n\n            // Extra buttons\n")
text = text.replace("            document.getElementById('forgot-trigger').addEventListener('click', (e) => { e.preventDefault(); showToastNotification('Vault Assist', 'Recovery keys targeted to email directory.'); });\n            document.getElementById('google-trigger').addEventListener('click', () => showToastNotification('Secure Router', 'Handshaking OAuth connection with Google...'));\n            document.getElementById('facebook-trigger').addEventListener('click', () => showToastNotification('Secure Router', 'Handshaking OAuth connection with Facebook...'));\n", "            const forgotTrigger = document.getElementById('forgot-trigger');\n            if (forgotTrigger) {\n                forgotTrigger.addEventListener('click', (e) => { e.preventDefault(); showToastNotification('Vault Assist', 'Recovery keys targeted to email directory.'); });\n            }\n            const googleTrigger = document.getElementById('google-trigger');\n            if (googleTrigger) {\n                googleTrigger.addEventListener('click', () => showToastNotification('Secure Router', 'Handshaking OAuth connection with Google...'));\n            }\n            const facebookTrigger = document.getElementById('facebook-trigger');\n            if (facebookTrigger) {\n                facebookTrigger.addEventListener('click', () => showToastNotification('Secure Router', 'Handshaking OAuth connection with Facebook...'));\n            }\n")
home_js.write_text(text, encoding='utf-8')

print('Updates applied.')
