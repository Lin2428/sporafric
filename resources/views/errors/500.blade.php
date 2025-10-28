<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page non trouvée</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: system-ui, -apple-system, sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            background-color: #94a3b8;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .background-image {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('https://images.unsplash.com/photo-1545972154-9bb223aac798?ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8Mnx8bm9tYWRlfGVufDB8fDB8fHww&fm=jpg&q=60&w=3000');
            background-size: cover;
            background-position: center;
            filter: brightness(0.6);
            z-index: -1;
        }

        .content {
            padding: 2rem;
            z-index: 1;
        }

        h1 {
            font-size: 8rem;
            font-weight: 700;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        h2 {
            font-size: 4rem;
            font-weight: 600;
            margin-bottom: 2rem;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        p {
            font-size: 1.5rem;
            margin-bottom: 3rem;
            color: #f1f5f9;
        }

        .link {
            color: #f1f5f9;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.2s;
        }

        .link:hover {
            text-decoration: underline;
        }

        .back-button {
            display: inline-flex;
            align-items: center;
            padding: 0.75rem 1.5rem;
            background-color: white;
            color: #475569;
            text-decoration: none;
            border-radius: 9999px;
            font-weight: 500;
            transition: all 0.2s;
        }

        .back-button:hover {
            background-color: #f1f5f9;
            transform: scale(1.05);
        }

        .back-button svg {
            width: 20px;
            height: 20px;
            margin-right: 0.5rem;
        }

        @media (max-width: 640px) {
            h1 {
                font-size: 4rem;
            }

            h2 {
                font-size: 2rem;
            }

            p {
                font-size: 1rem;
            }
        }
    </style>
</head>

<body>
    <div class="background-image"></div>
    <div class="content">
        <h1>404</h1>
        <h2>Page non trouvée</h2>
        <p>Désolé, nous n'avons pas pu trouver la page que vous recherchez.
            <br><a target="_blank" href="https://cg.linkedin.com/in/lin-marrion-daily-diaba-43543b231" class="link">By
                Lin</a>
        </p>
        <a href="{{ url('/') }}" class="back-button">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Retour à l'accueil
        </a>
    </div>
</body>

</html>
