<?php
session_start();

if (!isset($_SESSION['role_name']) || !isset($_SESSION['employee_id'])) {
    header("Location: login.php");
    exit();
}

// $role = $_SESSION['role'];
$employee_id = $_SESSION['employee_id'];

include('database.php');

$name = '';
$query = "SELECT lastname FROM employees WHERE employee_id = ?";
if ($stmt = $conn->prepare($query)) {
    $stmt->bind_param("i", $employee_id);
    $stmt->execute();
    $stmt->bind_result($name);
    $stmt->fetch();
    $stmt->close();
} else {
    echo "Error preparing the query.";
}

$conn->close();
include 'layout/header.php';
?>
<!-- Bootstrap CSS -->
<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

<style>
    body {
        background-color: #e0f7fa;
        /* Soft cyan background */
        font-family: Arial, sans-serif;
        font-size: 16px;
        line-height: 1.6;
        overflow-y: auto;
        /* Enable vertical scrolling */
    }

    .content {
        margin: 30px auto;
        max-width: 900px;
        padding: 20px;
        /* Add padding for better spacing */
        background-color: #ffffff;
        /* White background for content */
        border-radius: 10px;
        /* Rounded corners */
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        /* Subtle shadow */
        display: flex;
        flex-direction: column;
        /* Vertical layout */
        align-items: center;
        /* Center content horizontally */
        text-align: center;
        /* Center text */
    }

    .content h3 {
        font-weight: bold;
        margin-bottom: 15px;
        font-size: 24px;
        color: #00796b;
        /* Dark teal for headings */
    }

    .btn-map {
        background-color: #00796b;
        color: #fff;
        padding: 10px 20px;
        border-radius: 5px;
        text-decoration: none;
        border: none;
        transition: background-color 0.3s ease;
        cursor: pointer;
        display: block;
        margin: 20px auto;
        width: 150px;
    }

    .btn-map:hover {
        background-color: #004d40;
        /* Darker teal on hover */
    }

    .section-heading {
        border-bottom: 2px solid #00796b;
        padding-bottom: 5px;
        margin: 20px 0;
        width: 100%;
        /* Ensure the underline spans full width */
    }

    .contact-info {
        font-weight: bold;
        margin: 20px 0;
        background-color: #f1f8e9;
        /* Light green background for contact info */
        padding: 10px;
        border-radius: 5px;
        text-align: center;
        /* Center align the text */
    }

    .contact-info p {
        margin-bottom: 5px;
    }

    .map-container {
        margin-top: 20px;
    }

    .logo-container {
        text-align: center;
        margin-bottom: 20px;
    }

    .logo-container img {
        width: 120px;
        animation: fade-in 2s;
    }

    @keyframes fade-in {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    .decorative-background {
        background: linear-gradient(to bottom right, #00796b, #e0f7fa);
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        margin-bottom: 30px;
    }

    .decorative-line {
        width: 100px;
        height: 5px;
        background-color: #00796b;
        margin: 10px auto 20px auto;
        border-radius: 3px;
    }
</style>

<div class="d-flex align-items-stretch">
    <?php include 'layout/sidebar.php'; ?>
    <div class="main p-3" style="max-height: calc(100vh - 80px);overflow-y:scroll">
        <div class="container-fluid pl-5">
            <div class="decorative-background">
                <div class="logo-container">
                    <img src="assets/images/gm3-logo-small.png" alt="GM3 Builders Logo">
                    <div class="decorative-line"></div>
                </div>
                <div class="content">
                    <h3 class="mt-3">About Us</h3>
                    <p>At GM3 Builders, we pride ourselves on transforming visions into reality with precision and care. Founded on a commitment to quality craftsmanship and innovative design, our team of skilled professionals brings years of experience to every project, from residential homes to commercial spaces. We believe that collaboration and open communication are key to achieving exceptional results, and we work closely with our clients to ensure their unique needs are met.</p>
                    <p>With a focus on sustainability and modern building practices, GM3 Builders is dedicated to creating spaces that not only meet today’s demands but also stand the test of time. Let us help you build your dreams, one brick at a time.</p>

                    <h3 class="section-heading">WHO WE ARE</h3>
                    <p>GM3 Builders (GM3 for brevity) was founded by Engr. Geronimo A. Marbella Jr. "Jun" with the aim to become one of the most trusted construction service companies, specializing in civil, structural, and architectural works.</p>
                    <p>We are committed to being the best partner by completing our client's requirements on time, while continuously working with a professional and technical approach that emphasizes quality, safety, and excellence at a competitive price.</p>

                    <h3 class="section-heading">MISSION</h3>
                    <p>To build your satisfaction by being your best partner in the construction industry.</p>

                    <h3 class="section-heading">VISION</h3>
                    <p>To be recognized as the best partner for Civil, Structural & Architectural Works and Engineering Services.</p>

                    <h3 class="section-heading">COMPANY ADDRESS</h3>
                    <div class="contact-info">
                        <p>#618 Unit 219 Velasquez St. Brgy. San Juan, Taytay, Rizal</p>
                        <p>Tel: 02-75061786 | 0932-4488313 | 0921-2833083</p>
                        <p>Email: <a href="mailto:gm3builders@gmail.com">gm3builders@gmail.com</a></p>
                    </div>

                    <button class="btn-map mt-3" data-toggle="modal" data-target="#mapModal">View Map</button>

                    <div class="modal fade" id="mapModal" tabindex="-1" role="dialog" aria-labelledby="mapModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="mapModalLabel">Company Location</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body map-container">
                                    <iframe
                                        width="100%"
                                        height="400"
                                        frameborder="0"
                                        style="border:0"
                                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15447.639714660938!2d121.11974097878571!3d14.547143147816087!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397c71d6bcbf1fb%3A0xd2a99419e09bf060!2s618%2C%20219%20Velasquez%20St%2C%20Taytay%2C%201920%20Rizal!5e0!3m2!1sen!2sph!4v1730819601882!5m2!1sen!2sph"
                                        allowfullscreen=""
                                        loading="lazy">
                                    </iframe>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include './layout/script.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<?php include './layout/footer.php'; ?>