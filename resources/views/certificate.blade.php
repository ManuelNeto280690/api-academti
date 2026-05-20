<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Certificado - {{ $course_name }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@400;700&display=swap');
        
        body {
            margin: 0;
            padding: 0;
            background: #f0f2f5;
            font-family: 'Outfit', sans-serif;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            background: #f0f2f5;
            font-family: '{{ $font_family }}', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .certificate-container {
            width: 1000px;
            height: 700px;
            background: #fff;
            padding: 30px;
            position: relative;
            border: 20px solid {{ $primary_color }};
            box-shadow: 0 20px 50px rgba(0,0,0,0.1);
            background-image: {{ $background_image ? "url('$background_image')" : 'linear-gradient(rgba(238, 2, 4, 0.02) 2px, transparent 2px), linear-gradient(90deg, rgba(238, 2, 4, 0.02) 2px, transparent 2px)' }};
            background-size: 50px 50px;
            display: flex;
        }

        .inner-border {
            border: 2px solid {{ $primary_color }};
            width: 100%;
            height: 100%;
            padding: 30px 40px;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .header h1 {
            font-size: 48px;
            color: #1a1a1a;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 5px;
        }

        .header h2 {
            font-size: 18px;
            color: {{ $primary_color }};
            margin-top: 5px;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        .content {
            margin: 20px 0;
        }

        .content p {
            font-size: 20px;
            color: #555;
            margin: 8px 0;
        }

        .student-name {
            font-size: 45px;
            font-weight: 700;
            color: #1a1a1a;
            margin: 20px 0;
            border-bottom: 2px solid #ddd;
            display: inline-block;
            padding: 0 40px;
        }

        .course-name {
            font-size: 32px;
            color: {{ $primary_color }};
            font-weight: 700;
            display: block;
            margin: 20px 0;
        }

        .footer {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: 40px;
        }

        .signature {
            width: 250px;
            text-align: center;
        }

        .signature-line {
            border-top: 1px solid #1a1a1a;
            margin-top: 30px;
            padding-top: 10px;
            font-size: 16px;
            font-weight: 700;
        }

        .certificate-info {
            text-align: left;
            font-size: 14px;
            color: #888;
        }

        .logo {
            font-size: 30px;
            font-weight: 900;
            color: #1a1a1a;
        }
        .logo span {
            color: {{ $primary_color }};
        }

        @page {
            size: landscape;
            margin: 0;
        }

        @media print {
            body { 
                background: none; 
                -webkit-print-color-adjust: exact; 
                print-color-adjust: exact;
            }
            .certificate-container { 
                margin: 0; 
                box-shadow: none; 
                width: 100vw;
                height: 100vh;
                border-width: 15px;
            }
            .no-print { display: none; }
        }

        .print-btn {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: {{ $primary_color }};
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 50px;
            cursor: pointer;
            font-family: inherit;
            font-weight: bold;
            box-shadow: 0 10px 20px rgba(238, 2, 4, 0.3);
            z-index: 100;
            transition: transform 0.2s;
        }
        .print-btn:hover {
            transform: scale(1.05);
        }
    </style>
</head>
<body>
    <button class="print-btn no-print" onclick="window.print()">Imprimir PDF / Baixar</button>

    <div class="certificate-container" style="{{ $background_image ? "background-image: url('" . asset($background_image) . "'); background-size: cover;" : "" }}">
        <div class="inner-border">
            @if(empty($layout))
                {{-- Fallback: Old Static Design --}}
                <div class="header">
                    @if($show_logo)
                        <div class="logo">CEFTIC <span>ELITE</span></div>
                    @endif
                    <h1>Certificado</h1>
                    <h2>de Conclusão de Curso</h2>
                </div>

                <div class="content">
                    <p>Certificamos que</p>
                    <div class="student-name">{{ $student_name }}</div>
                    <p>concluiu com êxito o treinamento especializado em</p>
                    <div class="course-name">{{ $course_name }}</div>
                    <p>incluindo todas as avaliações e requisitos práticos necessários.</p>
                </div>

                <div class="footer">
                    <div class="certificate-info">
                        Código: {{ $certificate_code }}<br>
                        Data: {{ $completion_date }}<br>
                        Carga Horária: {{ $duration }}
                    </div>
                    <div class="signature">
                        @if($signature_image)
                            <img src="{{ asset($signature_image) }}" alt="Assinatura" style="max-height: 60px; margin-bottom: -10px;">
                        @else
                            <div style="font-family: cursive; font-size: 24px; color: {{ $secondary_color }};">{{ $instructor_name }}</div>
                        @endif
                        <div class="signature-line">{{ $instructor_title }}</div>
                    </div>
                </div>
            @else
                {{-- New Dynamic Layout --}}
                @foreach($layout as $block)
                    @php
                        $style = "position: absolute; ";
                        $style .= "left: " . ($block['x'] ?? 0) . "%; ";
                        $style .= "top: " . ($block['y'] ?? 0) . "%; ";
                        if(isset($block['width'])) $style .= "width: " . $block['width'] . "%; ";
                        if(isset($block['height'])) $style .= "height: " . $block['height'] . "%; ";
                        if(isset($block['fontSize'])) $style .= "font-size: " . $block['fontSize'] . "px; ";
                        if(isset($block['color'])) $style .= "color: " . ($block['color'] === 'primary' ? $primary_color : ($block['color'] === 'secondary' ? $secondary_color : $block['color'])) . "; ";
                        if(isset($block['fontWeight'])) $style .= "font-weight: " . $block['fontWeight'] . "; ";
                        if(isset($block['textAlign'])) $style .= "text-align: " . $block['textAlign'] . "; ";
                        if(isset($block['fontFamily'])) $style .= "font-family: " . $block['fontFamily'] . ", sans-serif; ";
                        if(isset($block['letterSpacing'])) $style .= "letter-spacing: " . $block['letterSpacing'] . "px; ";
                        if(isset($block['opacity'])) $style .= "opacity: " . ($block['opacity'] / 100) . "; ";
                        if(($block['textAlign'] ?? '') === 'center') $style .= "transform: translateX(-50%); ";
                    @endphp

                    <div style="{{ $style }}">
                        @if($block['type'] === 'student_name')
                            {{ $student_name }}
                        @elseif($block['type'] === 'student_bi')
                            {{ $student_bi }}
                        @elseif($block['type'] === 'course_name')
                            {{ $course_name }}
                        @elseif($block['type'] === 'course_duration')
                            {{ $course_duration }} Horas
                        @elseif($block['type'] === 'course_level')
                            {{ $course_level }}
                        @elseif($block['type'] === 'trainer_name')
                            {{ $trainer_name }}
                        @elseif($block['type'] === 'date')
                            {{ $completion_date }}
                        @elseif($block['type'] === 'certificate_code')
                            {{ $certificate_code }}
                        @elseif($block['type'] === 'instructor_name')
                            {{ $instructor_name }}
                        @elseif($block['type'] === 'instructor_title')
                            {{ $instructor_title }}
                        @elseif($block['type'] === 'duration')
                            {{ $duration }}
                        @elseif($block['type'] === 'static_text')
                            {!! nl2br(e($block['content'] ?? '')) !!}
                        @elseif($block['type'] === 'image')
                            <img src="{{ asset($block['src'] ?? '') }}" style="width: 100%; height: auto; border-radius: {{ $block['borderRadius'] ?? 0 }}px;">
                        @elseif($block['type'] === 'logo')
                             <div class="logo" style="font-size: inherit;">CEFTIC <span style="color: {{ $primary_color }}">ELITE</span></div>
                        @elseif($block['type'] === 'signature')
                            @if($signature_image)
                                <img src="{{ asset($signature_image) }}" style="max-height: 80px; width: auto;">
                            @else
                                <div style="font-family: cursive; font-size: 1.2em;">{{ $instructor_name }}</div>
                            @endif
                        @elseif($block['type'] === 'qr_code')
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ urlencode($verification_url) }}" style="width: 100%; height: auto;">
                        @endif
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</body>
</html>
