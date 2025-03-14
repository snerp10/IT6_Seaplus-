@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            <div class="card shadow-lg rounded-lg hover-card">
                <div class="card-header bg-white py-3">
                    <h3 class="m-0 fw-bold">
                        <i class="fas fa-shield-alt me-2 gold-text"></i>
                        <span class="myrtle-text">Privacy Policy</span>
                    </h3>
                </div>

                <div class="card-body p-4">
                    <div class="mb-4 lead">
                        This privacy notice discloses the privacy practices for KSM SeaPlus+ (the "Company"). This privacy notice applies to information collected by the Company through the website.
                    </div>

                    <div class="mb-4">
                        <h5 class="fw-bold myrtle-text mb-3">Information Collection</h5>
                        <p>
                            The Company is the sole owner of the information collected on the Website. The Company may collect information from you in the following ways:
                        </p>

                        <ul class="list-group list-group-flush mb-3">
                            <li class="list-group-item bg-light py-3">
                                <i class="fas fa-user-circle gold-text me-2"></i> Information you provide to the Company through the Website, such as your name and email address
                            </li>
                            <li class="list-group-item bg-light py-3">
                                <i class="fas fa-cookie gold-text me-2"></i> Information the Company collects automatically, such as your IP address, browser type, and operating system, through the use of cookies, web beacons, and other tracking technologies
                            </li>
                            <li class="list-group-item bg-light py-3">
                                <i class="fas fa-chart-bar gold-text me-2"></i> Information the Company receives from third parties, such as analytics providers
                            </li>
                        </ul>
                    </div>

                    <div class="mb-4">
                        <h5 class="fw-bold myrtle-text mb-3">Use of Information</h5>
                        <p>
                            The Company will use the information collected to:
                        </p>

                        <ul class="list-group list-group-flush mb-3">
                            <li class="list-group-item bg-light py-3">
                                <i class="fas fa-hands-helping gold-text me-2"></i> Provide services and support to you
                            </li>
                            <li class="list-group-item bg-light py-3">
                                <i class="fas fa-tools gold-text me-2"></i> Improve the Website and its content
                            </li>
                            <li class="list-group-item bg-light py-3">
                                <i class="fas fa-reply gold-text me-2"></i> Respond to your inquiries and requests
                            </li>
                            <li class="list-group-item bg-light py-3">
                                <i class="fas fa-envelope-open-text gold-text me-2"></i> Send you information and promotional materials
                            </li>
                            <li class="list-group-item bg-light py-3">
                                <i class="fas fa-magic gold-text me-2"></i> Enhance your experience on the Website
                            </li>
                            <li class="list-group-item bg-light py-3">
                                <i class="fas fa-chart-line gold-text me-2"></i> Perform analytics and conduct research
                            </li>
                        </ul>
                    </div>

                    <div class="mb-4">
                        <h5 class="fw-bold myrtle-text mb-2">Information Sharing</h5>
                        <p>
                            The Company will not sell, rent, or trade your personal information to any third party without your consent.
                        </p>
                    </div>

                    <div class="mb-4">
                        <h5 class="fw-bold myrtle-text mb-2">Data Security</h5>
                        <p>
                            The Company will take reasonable steps to protect your personal information from unauthorized access, disclosure, or use.
                        </p>
                    </div>

                    <div class="mb-4">
                        <h5 class="fw-bold myrtle-text mb-2">Your Rights</h5>
                        <p>
                            You have the right to request access to your personal information, to request correction of any errors or inaccuracies in your personal information, and to request that the Company delete your personal information.
                        </p>
                    </div>

                    <div class="mb-4">
                        <h5 class="fw-bold myrtle-text mb-2">Policy Changes</h5>
                        <p>
                            The Company reserves the right to modify this privacy notice at any time. Any changes will be posted to this page, and it is your responsibility to review this page periodically for any changes.
                        </p>
                    </div>

                    <div class="alert alert-info border-left-primary mt-5">
                        <p class="mb-0">
                            Last updated: {{ date('F d, Y') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

