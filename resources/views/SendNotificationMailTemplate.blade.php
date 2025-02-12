<!doctype html>
<html lang="en">

<head>
    <title>Notification</title>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <!-- Bootstrap CSS v5.2.1 -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN"
        crossorigin="anonymous" />
</head>

<body>
    <header>
        <!-- place navbar here -->
    </header>
    <main>
        <div class="bg-primary" style="height: 50px; width: 100%; display: flex; align-items: center; padding-left: 20px; flex-direction: row;">
            <p style="font-size: 24px; color: white; font-weight: 600; padding-top: 16px;">JOBSPOT</p>
        </div>
        <div style="padding: 20px;">

            <p style="font-size: 18px;">
                Hi {{$receiver}},
            </p>

            <p>
                You have received a new application for the {{$position}} position. You can review the application in app directly.
            </p>
            <p>
                Thank you!
            </p>
            <p>Best regards, <br>
                Chenter PHAI <br>
                CEO of JobSpot <br>
                IntelliNex
            </p>

            <a href="" class="btn btn-primary">
                Open App
            </a>

        </div>
    </main>
    <footer>
        <!-- place footer here -->
    </footer>
    <!-- Bootstrap JavaScript Libraries -->
    <script
        src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
        integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
        crossorigin="anonymous"></script>

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"
        integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+"
        crossorigin="anonymous"></script>
</body>

</html>