<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f7f9;
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
        }
        .wrapper {
            width: 100%;
            table-layout: fixed;
            background-color: #f4f7f9;
            padding-bottom: 40px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            margin-top: 40px;
        }
        .header {
            background-color: #0f172a;
            padding: 40px 20px;
            text-align: center;
        }
        .header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 28px;
            font-weight: 700;
            letter-spacing: -0.5px;
        }
        .header span {
            color: #ef4444; /* Vermelho Ceftic */
        }
        .content {
            padding: 40px 30px;
            color: #334155;
            line-height: 1.6;
        }
        .content h2 {
            color: #0f172a;
            font-size: 22px;
            margin-top: 0;
        }
        .footer {
            padding: 24px;
            text-align: center;
            font-size: 13px;
            color: #94a3b8;
            background-color: #f8fafc;
        }
        .button {
            display: inline-block;
            padding: 14px 28px;
            background-color: #ef4444;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            margin: 20px 0;
        }
        .social-links {
            margin-top: 20px;
        }
        .social-links a {
            margin: 0 10px;
            color: #64748b;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container">
            <div class="header">
                <h1>Ceftic<span>.</span></h1>
            </div>
            
            <div class="content">
                @yield('content')
            </div>
            
            <div class="footer">
                <p>&copy; {{ date('Y') }} Ceftic Angola. Todos os direitos reservados.</p>
                <p>Estrada de Catete, Luanda, Angola</p>
                <div class="social-links">
                    <a href="#">LinkedIn</a>
                    <a href="#">Instagram</a>
                    <a href="#">Facebook</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
