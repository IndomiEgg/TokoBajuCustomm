<?php $this->extend('layout/template') ?>

<?php $this->section('content') ?>
<div class="container mx-auto py-12">
    <div class="max-w-md mx-auto bg-white text-black rounded-lg p-6 shadow">
        <h2 class="text-2xl font-bold mb-4">Reset Admin Password</h2>

        <?php if(session()->getFlashdata('error')): ?>
            <div class="alert alert-danger mb-3"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>
        <?php if(session()->getFlashdata('success')): ?>
            <div class="alert alert-success mb-3"><?= session()->getFlashdata('success') ?></div>
        <?php endif; ?>

        <form action="#" method="post">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" name="email" class="input w-full" required>
            </div>
            <div class="flex items-center justify-between">
                <button class="btn-luxury btn-luxury-solid">Send Reset Link</button>
                <a href="<?= base_url('admin/login') ?>" class="text-sm">Back to login</a>
            </div>
        </form>
    </div>
</div>

<?php $this->endSection() ?>
