<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

$page_title = 'About Us';
include '../includes/header.php';
?>

<!-- Hero Section -->
<div class="bg-primary text-white py-5 mb-5">
    <div class="container text-center">
        <h1 class="display-4 fw-bold mb-3">About Kulmiye</h1>
        <p class="lead mb-0 opacity-75">Empowering minds through insightful stories and knowledge sharing.</p>
    </div>
</div>

<!-- Main Content -->
<div class="container mb-5">
    <div class="row align-items-center mb-5">
        <div class="col-lg-6 mb-4 mb-lg-0">
            <h2 class="fw-bold mb-4">Our Mission</h2>
            <p class="lead text-secondary mb-4">
                At Kulmiye, we believe in the power of words to connect, inspire, and transform. our platform is dedicated to bringing you the most compelling stories from around the globe.
            </p>
            <p class="text-secondary">
                Founded in 2024, Kulmiye has grown from a small passion project into a thriving community of writers, thinkers, and readers. We cover a diverse range of topics from cutting-edge technology to lifestyle trends, ensuring there's something for everyone.
            </p>
        </div>
        <div class="col-lg-6">
            <div class="bg-light rounded-4 p-5 text-center h-100 d-flex align-items-center justify-content-center">
                <i class="bi bi-people-fill display-1 text-primary opacity-50"></i>
            </div>
        </div>
    </div>

    <div class="row align-items-center mb-5 flex-lg-row-reverse">
        <div class="col-lg-6 mb-4 mb-lg-0">
            <h2 class="fw-bold mb-4">What We Do</h2>
            <ul class="list-unstyled">
                <li class="d-flex mb-4">
                    <div class="flex-shrink-0">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="bi bi-journal-richtext fs-5"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h4 class="h5 fw-bold">Quality Content</h4>
                        <p class="text-secondary mb-0">We curate high-quality articles that provide value, depth, and new perspectives to our readers.</p>
                    </div>
                </li>
                <li class="d-flex mb-4">
                    <div class="flex-shrink-0">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="bi bi-share fs-5"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h4 class="h5 fw-bold">Community Driven</h4>
                        <p class="text-secondary mb-0">We foster a community where voices can be heard and ideas can be shared freely and respectfully.</p>
                    </div>
                </li>
            </ul>
        </div>
        <div class="col-lg-6">
            <div class="bg-light rounded-4 p-5 text-center h-100 d-flex align-items-center justify-content-center">
                <i class="bi bi-lightbulb-fill display-1 text-primary opacity-50"></i>
            </div>
        </div>
    </div>
    
    <!-- Team Section (Optional) -->
    <div class="text-center mt-5 pt-5 border-top">
        <h2 class="fw-bold mb-5">Meet the Team</h2>
        <div class="row g-4 justify-content-center">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 hover-lift">
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <div class="bg-secondary bg-opacity-10 rounded-circle mx-auto d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                <i class="bi bi-person fs-1 text-secondary"></i>
                            </div>
                        </div>
                        <h5 class="fw-bold">Ahmed Ali</h5>
                        <p class="text-muted small mb-0">Founder & Editor in Chief</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 hover-lift">
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <div class="bg-secondary bg-opacity-10 rounded-circle mx-auto d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                <i class="bi bi-person fs-1 text-secondary"></i>
                            </div>
                        </div>
                        <h5 class="fw-bold">Sarah Mohamed</h5>
                        <p class="text-muted small mb-0">Lead Writer</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
