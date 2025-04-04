<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes Cinematográficos</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #1a2a6c, #b21f1f, #fdbb2d);
            background-size: 400% 400%;
            animation: gradient 15s ease infinite;
            height: 100vh;
            margin: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: white;
            text-align: center;
        }

        @keyframes gradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .container {
            background-color: rgba(0, 0, 0, 0.7);
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.5);
            max-width: 800px;
            width: 90%;
        }

        h1 {
            font-size: 2.5rem;
            margin-bottom: 40px;
            text-transform: uppercase;
            letter-spacing: 3px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }

        .button-container {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-top: 20px;
        }

        .btn {
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid white;
            color: white;
            padding: 15px 25px;
            font-size: 1.1rem;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(5px);
        }

        .btn:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }

        .logo {
            width: 100px;
            margin-bottom: 20px;
        }

        footer {
            margin-top: 40px;
            font-size: 0.9rem;
            opacity: 0.8;
        }

        @media (max-width: 600px) {
            .button-container {
                grid-template-columns: 1fr;
            }
            
            h1 {
                font-size: 1.8rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <img src="logo.jpg" alt="Logo Cine" class="logo">
        <h1>Reportes Cinematográficos</h1>
        
        <div class="button-container">
            <a href="reporte_peliculas.php" class="btn">Películas</a>
            <a href="reporte_actores.php" class="btn">Actores</a>
            <a href="reporte_estudios.php" class="btn">Estudios</a>
            <a href="Reporte.php" class="btn"> Reporte Actores en Peliculas</a>
        </div>
        
        <footer>
            Sistema de Reportes Cinematográficos &copy; <?php echo date('Y'); ?>
        </footer>
    </div>
</body>
</html>