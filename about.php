<?php
$about = $conn->query("SELECT * FROM about_content LIMIT 1")->fetch_assoc();
?>
<section id="about" class="container mx-auto mt-16 p-8 about-iphone">
    <div class="max-w-3xl mx-auto bg-white rounded-2xl shadow-lg p-8">
        <h2 class="text-3xl font-semibold text-gray-900 text-center mb-6">
            <?php echo htmlspecialchars($about['main_title'] ?? 'About iPhone Shop'); ?>
        </h2>
        <p class="text-gray-600 text-center text-lg mb-6">
            <?php echo htmlspecialchars($about['main_description'] ?? 'We bring you authentic iPhones, fast delivery, and trusted support.'); ?>
        </p>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mt-8 features">
            <div class="text-center feature-item">
                <div class="feature-icon">
                    <i class="<?php echo htmlspecialchars($about['feature1_icon'] ?? 'fas fa-shipping-fast'); ?>"></i>
                </div>
                <h3 class="text-xl font-semibold mt-4"><?php echo htmlspecialchars($about['feature1_title'] ?? 'Fast Delivery'); ?></h3>
                <p class="text-gray-600 mt-2"><?php echo htmlspecialchars($about['feature1_description'] ?? ''); ?></p>
            </div>
            <div class="text-center feature-item">
                <div class="feature-icon">
                    <i class="<?php echo htmlspecialchars($about['feature2_icon'] ?? 'fas fa-shield-alt'); ?>"></i>
                </div>
                <h3 class="text-xl font-semibold mt-4"><?php echo htmlspecialchars($about['feature2_title'] ?? 'Secure Shopping'); ?></h3>
                <p class="text-gray-600 mt-2"><?php echo htmlspecialchars($about['feature2_description'] ?? ''); ?></p>
            </div>
            <div class="text-center feature-item">
                <div class="feature-icon">
                    <i class="<?php echo htmlspecialchars($about['feature3_icon'] ?? 'fas fa-headset'); ?>"></i>
                </div>
                <h3 class="text-xl font-semibold mt-4"><?php echo htmlspecialchars($about['feature3_title'] ?? '24/7 Support'); ?></h3>
                <p class="text-gray-600 mt-2"><?php echo htmlspecialchars($about['feature3_description'] ?? ''); ?></p>
            </div>
        </div>
    </div>
</section>

<style>
    .about-iphone, .about-iphone *{ font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; }
    .about-iphone .shadow-lg{ box-shadow: 0 12px 30px rgba(0,0,0,0.10); }
    .about-iphone i{ color: #374151; }
    /* Feature icon styles */
    .features .feature-item{ padding: 1rem 0; }
    .feature-icon{ width: 56px; height: 56px; margin: 0 auto; border-radius: 12px; display:flex; align-items:center; justify-content:center; }
    .feature-icon i, .feature-icon svg{ width: 28px; height: 28px; stroke-width: 1.6; }
    .feature-item h3{ margin-top: 0.75rem; }
</style>
