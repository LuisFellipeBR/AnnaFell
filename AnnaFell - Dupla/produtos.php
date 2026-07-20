<?php
// produtos.php - Lista completa de produtos da loja

function getProdutos() {
    $produtos = [
        1 => [
            'id'        => 1,
            'nome'      => 'Camiseta Luminosity',
            'descricao' => 'Camiseta 100% algodão com estampa competitiva',
            'preco'     => 79.90,
            'imagem'    => 'img/produto1.jpg'
        ],
        2 => [
            'id'        => 2,
            'nome'      => 'Mouse RGB',
            'descricao' => 'Mouse óptico com 7 botões e luzes coloridas',
            'preco'     => 149.90,
            'imagem'    => 'img/produto2.jpg'
        ],
        3 => [
            'id'        => 3,
            'nome'      => 'Teclado Mecânico',
            'descricao' => 'Teclado mecânico com LED azul e switches blue',
            'preco'     => 299.90,
            'imagem'    => 'img/produto3.jpg'
        ],
        4 => [
            'id'        => 4,
            'nome'      => 'Headset Gamer',
            'descricao' => 'Headset com som surround 7.1 e microfone',
            'preco'     => 199.90,
            'imagem'    => 'img/produto4.jpg'
        ],
        5 => [
            'id'        => 5,
            'nome'      => 'Monitor 24"',
            'descricao' => 'Monitor LED Full HD 75Hz',
            'preco'     => 899.90,
            'imagem'    => 'img/produto5.jpg'
        ],
        6 => [
            'id'        => 6,
            'nome'      => 'Cadeira Gamer',
            'descricao' => 'Cadeira reclinável com apoio de cabeça',
            'preco'     => 1299.90,
            'imagem'    => 'img/produto6.jpg'
        ],
        7 => [
            'id'        => 7,
            'nome'      => 'SSD 480GB',
            'descricao' => 'SSD Kingston SATA 480GB',
            'preco'     => 299.90,
            'imagem'    => 'img/produto7.jpg'
        ],
        8 => [
            'id'        => 8,
            'nome'      => 'HD Externo 1TB',
            'descricao' => 'HD portátil USB 3.0',
            'preco'     => 399.90,
            'imagem'    => 'img/produto8.jpg'
        ],
        9 => [
            'id'        => 9,
            'nome'      => 'Placa de Vídeo RTX 4060',
            'descricao' => '8GB GDDR6',
            'preco'     => 1990.90,
            'imagem'    => 'img/produto9.jpg'
        ],
        10 => [
            'id'        => 10,
            'nome'      => 'Fonte 500W',
            'descricao' => 'Fonte certificada 80 Plus',
            'preco'     => 249.90,
            'imagem'    => 'img/produto10.jpg'
        ],
        11 => [
            'id'        => 11,
            'nome'      => 'Gabinete Gamer',
            'descricao' => 'Com laterais em vidro e fans RGB',
            'preco'     => 349.90,
            'imagem'    => 'img/produto11.jpg'
        ],
        12 => [
            'id'        => 12,
            'nome'      => 'Mousepad RGB',
            'descricao' => 'Mousepad médio com iluminação',
            'preco'     => 89.90,
            'imagem'    => 'img/produto12.jpg'
        ],
        13 => [
            'id'        => 13,
            'nome'      => 'Webcam Full HD',
            'descricao' => 'Webcam com microfone embutido',
            'preco'     => 199.90,
            'imagem'    => 'img/produto13.jpg'
        ],
        14 => [
            'id'        => 14,
            'nome'      => 'Microfone de Mesa',
            'descricao' => 'Microfone condensador USB',
            'preco'     => 149.90,
            'imagem'    => 'img/produto14.jpg'
        ],
        15 => [
            'id'        => 15,
            'nome'      => 'Asus Tuf Gaming b450m',
            'descricao' => 'ótima placa mãe de entrada/intermediária para os seus componentes, arquitetura militar',
            'preco'     => 1120.90,
            'imagem'    => 'img/produto15.jpg'
        ]
    ];
    return $produtos;
}
?>