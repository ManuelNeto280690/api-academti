<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Testimonial;

class TestimonialSeeder extends Seeder
{
    public function run(): void
    {
        $testimonials = [
            [
                'name' => 'Gonçalo Ferreira',
                'role' => 'Cloud Security Engineer',
                'company' => 'Critical Software',
                'image' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=100&h=100&fit=crop&crop=face',
                'content' => "Acabei de concluir a formação de Pentest na AcademTI. A profundidade técnica dos labs é impressionante. É de longe o melhor centro de formação em Portugal para quem quer realmente 'meter as mãos na massa'. 🚀",
                'likes' => 124,
                'comments' => 18,
                'date_string' => '2 dias',
                'verified' => true,
            ],
            [
                'name' => 'Beatriz Ribeiro',
                'role' => 'Frontend Developer',
                'company' => 'Farfetch',
                'image' => 'https://images.unsplash.com/photo-1494790108755-2616b612b786?w=100&h=100&fit=crop&crop=face',
                'content' => 'Mudar de carreira não é fácil, mas a mentoria da AcademTI fez toda a diferença. O foco em React e Next.js com projetos reais preparou-me para os desafios que encontro hoje na Farfetch. Grata por toda a equipa!',
                'likes' => 89,
                'comments' => 12,
                'date_string' => '1 semana',
                'verified' => true,
            ],
            [
                'name' => 'Ricardo Manteiga',
                'role' => 'SOC Analyst',
                'company' => 'Sonae',
                'image' => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=100&h=100&fit=crop&crop=face',
                'content' => 'O curso de Analista de Segurança Cibernética superou as minhas expectativas. O suporte dos formadores é personalizado e focado no mercado de trabalho português. Recomendo vivamente a qualquer profissional de IT.',
                'likes' => 156,
                'comments' => 24,
                'date_string' => '3 dias',
                'verified' => true,
            ],
            [
                'name' => 'Sofia Antunes',
                'role' => 'Data Scientist',
                'company' => 'Nokia',
                'image' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=100&h=100&fit=crop&crop=face',
                'content' => 'A formação em IA Generativa abriu-me horizontes que não imaginava. A AcademTI está na vanguarda do que se faz em tecnologia. O ambiente de comunidade é incrível e as oportunidades de networking são reais.',
                'likes' => 210,
                'comments' => 45,
                'date_string' => '5 h',
                'verified' => true,
            ],
            [
                'name' => 'Tiago Silva',
                'role' => 'DevOps Architect',
                'company' => 'Talkdesk',
                'image' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=100&h=100&fit=crop&crop=face',
                'content' => 'A transição para Cloud Native foi muito mais simples com o apoio da AcademTI. Os instrutores vivem os problemas que ensinam no dia a dia. Altamente recomendado para séniores.',
                'likes' => 92,
                'comments' => 14,
                'date_string' => '4 dias',
                'verified' => true,
            ],
            [
                'name' => 'Marta Oliveira',
                'role' => 'Cybersecurity Analyst',
                'company' => 'NOS',
                'image' => 'https://images.unsplash.com/photo-1580489944761-15a19d654956?w=100&h=100&fit=crop&crop=face',
                'content' => 'O que mais me impressionou foi o suporte pós-curso. A AcademTI não te deixa sozinho depois de receberes o certificado. O networking na comunidade é o melhor de Portugal.',
                'likes' => 178,
                'comments' => 31,
                'date_string' => '12 h',
                'verified' => true,
            ],
            [
                'name' => 'João Pereira',
                'role' => 'Full Stack Developer',
                'company' => 'OutSystems',
                'image' => 'https://images.unsplash.com/photo-1519345182560-3f2917c472ef?w=100&h=100&fit=crop&crop=face',
                'content' => 'Projetos reais, stack moderna e uma rede de contactos incrível. A AcademTI é o atalho que eu precisava para subir de nível na minha carreira como desenvolvedor Full Stack.',
                'likes' => 134,
                'comments' => 19,
                'date_string' => '6 h',
                'verified' => true,
            ]
        ];

        foreach ($testimonials as $data) {
            Testimonial::create($data);
        }
    }
}
