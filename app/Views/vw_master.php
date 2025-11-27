<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link id="favicon" rel="shortcut icon" href="<?= base_url("logo_light.png") ?>" type="image/png">
    <title><?= $this->renderSection('page_title', true) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <script src="https://kit.fontawesome.com/601ba7fe41.js" crossorigin="anonymous"></script>
    <style>
        body {
            background-color: #dbdbdbff !important;
        }

        #logo-container {
            display: flex;
            align-items: center;

            & img {
                cursor: pointer;
            }

            & span {
                margin-left: 16px;
                cursor: pointer;
            }
        }
    </style>
    <?= $this->renderSection('style') ?>
</head>

<body>
    <nav class="navbar bg-dark" data-bs-theme="dark">
        <div class="container-fluid">
            <div id="logo-container">
                <!-- Brand Image -->
                <img src="<?= base_url("logo_light.png") ?>" alt="Logo" width="50" height="40" class="d-inline-block align-text-top" onclick="location.href = '/admin'">
                <!-- Brand Text -->
                <span class="navbar-brand mb-0 h1" onclick="location.href = '/admin'">BIONIC NATURA</span>
            </div>

        </div>
    </nav>
    <?= $this->renderSection('content') ?>
    <?= $this->renderSection('footer') ?>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.min.js" integrity="sha384-G/EV+4j2dNv+tEPo3++6LCgdCROaejBqfUeNjuKAiuXbjrxilcCdDz6ZAVfHWe1Y" crossorigin="anonymous"></script>
    <script>
        // get icon id
        const faviconLink = document.getElementById('favicon');

        // function for changing icon color based on theme
        function setFavicon() {
            if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
                // Dark mode
                faviconLink.href = '<?= base_url("logo_light.png") ?>';
            } else {
                // Light mode or no preference
                faviconLink.href = '<?= base_url("logo_dark.png") ?>';
            }
        }

        // Initial check
        setFavicon();

        // Listen for changes in the system theme
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', setFavicon);
    </script>
    <?= $this->renderSection('script') ?>
</body>

</html>