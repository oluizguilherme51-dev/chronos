CREATE DATABASE IF NOT EXISTS chronos
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE chronos;

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    perfil ENUM('cliente', 'admin') DEFAULT 'cliente'
);

CREATE TABLE colecoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    descricao VARCHAR(255) NOT NULL
);

CREATE TABLE relogios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    colecao_id INT NOT NULL,
    marca VARCHAR(100) NOT NULL,
    modelo VARCHAR(150) NOT NULL,
    referencia VARCHAR(100) NOT NULL,
    descricao TEXT NOT NULL,
    preco DECIMAL(12,2) NOT NULL,
    estoque INT NOT NULL,
    imagem VARCHAR(255) NOT NULL,
    FOREIGN KEY (colecao_id) REFERENCES colecoes(id)
);

CREATE TABLE vendas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    data_venda DATETIME DEFAULT CURRENT_TIMESTAMP,
    pagamento ENUM('PIX', 'Boleto', 'Cartão') NOT NULL,
    valor_total DECIMAL(14,2) NOT NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

CREATE TABLE itens_venda (
    id INT AUTO_INCREMENT PRIMARY KEY,
    venda_id INT NOT NULL,
    relogio_id INT NOT NULL,
    quantidade INT NOT NULL,
    valor_unitario DECIMAL(12,2) NOT NULL,
    FOREIGN KEY (venda_id) REFERENCES vendas(id),
    FOREIGN KEY (relogio_id) REFERENCES relogios(id)
);

INSERT INTO colecoes (nome, descricao) VALUES
('Heritage', 'A elegância dos clássicos atemporais.'),
('Performance', 'Precisão e desempenho em cada movimento.'),
('Haute Horlogerie', 'A excelência da alta relojoaria.'),
('Exclusivité', 'Peças extraordinárias e exclusivas.');

-- Catalogo Chronos
-- Valores de referencia para simulacao academica

INSERT INTO relogios
(colecao_id, marca, modelo, referencia, descricao, preco, estoque, imagem)
VALUES

(1, 'Rolex', 'Datejust 41', '126300',
'Relógio automático com caixa de 41 mm em aço Oystersteel, mostrador azul e indicação de data. Combina elegância clássica e precisão mecânica.',
75400.00, 5, 'datejust.jpg'),

(1, 'Cartier', 'Tank Must XL', 'WSTA0040',
'Relógio com caixa retangular em aço, algarismos romanos, movimento automático e pulseira de couro.',
38800.00, 4, 'tankmust.jpg'),

(1, 'Jaeger-LeCoultre', 'Reverso Tribute Monoface', 'Q713842J',
'Relógio de corda manual com caixa reversível em aço, mostrador prateado e design inspirado no estilo Art Déco.',
57329.00, 3, 'reverso.jpg'),

(1, 'Rolex', '1908 Platinum', '52506',
'Relógio de 39 mm em platina com mostrador azul-glacial de textura guilloché e pequenos segundos.',
276700.00, 2, 'rolex1908.jpg'),

(2, 'Rolex', 'Submariner Date', '126610LN',
'Relógio automático de mergulho com caixa de 41 mm em aço Oystersteel, mostrador preto e aro giratório unidirecional.',
96000.00, 5, 'submariner.jpg'),

(2, 'Rolex', 'Cosmograph Daytona', '126500LN',
'Cronógrafo automático de 40 mm em aço Oystersteel, mostrador branco e aro Cerachrom com escala taquimétrica.',
142800.00, 3, 'daytona.jpg'),

(2, 'Rolex', 'Yacht-Master 42', '226627',
'Relógio automático com caixa de 42 mm em titânio RLX, mostrador preto e aro giratório bidirecional.',
136000.00, 3, 'yachtmaster.jpg'),

(2, 'Omega', 'Speedmaster Moonwatch Professional',
'310.30.42.50.01.001',
'Cronógrafo de corda manual com caixa de 42 mm em aço, mostrador preto e cristal Hesalite. Modelo ligado à história da exploração espacial.',
40981.00, 4, 'speedmaster.jpg'),

(3, 'Audemars Piguet', 'Royal Oak Selfwinding', '15510ST',
'Relógio automático em aço com caixa de 41 mm, aro octogonal, parafusos aparentes e mostrador azul com padrão Grande Tapisserie.',
357179.00, 2, 'royaloak.jpg'),

(3, 'Vacheron Constantin', 'Overseas', '4520V/210A-B128',
'Relógio automático de 41 mm em aço inoxidável, mostrador azul e design esportivo sofisticado.',
235875.00, 2, 'overseas.jpg'),

(3, 'Hublot', 'Big Bang Unico Titanium', '441.NX.1171.RX',
'Cronógrafo automático de 42 mm em titânio, mostrador esqueletizado, movimento HUB1280 e pulseira de borracha preta.',
97443.00, 3, 'bigbang.jpg'),

(3, 'Cartier', 'Santos-Dumont Platinum', 'Santos-Dumont XL',
'Relógio com caixa em platina, movimento mecânico de corda manual e design inspirado na tradição da coleção Santos.',
181000.00, 2, 'santosdumont.jpg'),

(4, 'Patek Philippe', 'Nautilus', '5711/1A-010',
'Relógio automático em aço inoxidável com mostrador azul, pulseira integrada e caixa inspirada nas escotilhas marítimas.',
877085.00, 1, 'nautilus.jpg'),

(4, 'Richard Mille', 'RM 011 Felipe Massa Titanium', 'RM 011-FM',
'Cronógrafo automático com caixa em titânio, mostrador esqueletizado e função flyback.',
941801.00, 1, 'richardmille.jpg'),

(4, 'Rolex', 'Day-Date 40', '228238',
'Relógio automático de 40 mm em ouro amarelo de 18 quilates, pulseira President e indicação do dia da semana e da data.',
411000.00, 2, 'daydate.jpg'),

(4, 'Rolex', 'Land-Dweller 36', 'Land-Dweller Platinum',
'Relógio de 36 mm em platina, mostrador azul-glacial com padrão de colmeia e detalhes em diamantes.',
841800.00, 1, 'landdweller.jpg');