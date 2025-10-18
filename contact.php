<?php
$contact = $conn->query("SELECT * FROM contact_info LIMIT 1")->fetch_assoc();
?>
<section id="contact" class="container mx-auto mt-16 p-8 about-iphone">
    <div class="max-w-4xl mx-auto bg-white rounded-2xl shadow-lg p-8 mb-16 contact-iphone">
        <h2 class="text-3xl font-semibold text-gray-900 text-center mb-6">Contact iPhone Shop</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="contact-info">
                <h3 class="text-xl font-semibold mb-4">Get in Touch</h3>
                <ul class="space-y-4">
                    <li class="flex items-center">
                        <i class="fas fa-map-marker-alt text-gray-700 w-8"></i>
                        <span><?php echo htmlspecialchars($contact['address'] ?? ''); ?></span>
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-phone text-gray-700 w-8"></i>
                        <span><?php echo htmlspecialchars($contact['phone'] ?? ''); ?></span>
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-envelope text-gray-700 w-8"></i>
                        <span><?php echo htmlspecialchars($contact['email'] ?? ''); ?></span>
                    </li>
                </ul>
                <div class="mt-6 flex space-x-4">
                    <?php if (!empty($contact['facebook_url'])): ?>
                        <a href="<?php echo htmlspecialchars($contact['facebook_url']); ?>" 
                           class="text-gray-700 hover:text-gray-900" target="_blank">
                            <i class="fab fa-facebook fa-2x"></i>
                        </a>
                    <?php endif; ?>
                    <?php if (!empty($contact['tiktok'])): ?>
                        <a href="<?php echo htmlspecialchars($contact['tiktok']); ?>" 
                           class="text-gray-700 hover:text-gray-900" target="_blank">
                           <i class="fab fa-tiktok fa-2x"></i>
                        </a>
                    <?php endif; ?>
                    <?php if (!empty($contact['instagram_url'])): ?>
                        <a href="<?php echo htmlspecialchars($contact['instagram_url']); ?>" 
                           class="text-gray-700 hover:text-gray-900" target="_blank">
                            <i class="fab fa-instagram fa-2x"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <form class="space-y-4">
                    <div>
                        <input type="text" placeholder="Your Name" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-gray-500">
                    </div>
                    <div>
                        <input type="email" placeholder="Your Email" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-gray-500">
                    </div>
                    <div>
                        <textarea placeholder="Your Message" rows="4" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-gray-500"></textarea>
                    </div>
                    <button type="submit" class="bg-black text-white px-6 py-2 rounded-full hover:bg-gray-900 transition">
                        Send Message
                    </button>
                </form>
        </div>
    </div>
</section>

<style>
    .about-iphone, .about-iphone *{ font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; }
    .contact-iphone .shadow-lg{ box-shadow: 0 12px 30px rgba(0,0,0,0.10); }
    .contact-iphone i{ color: #374151; }
</style>