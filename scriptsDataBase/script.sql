CREATE TABLE baixa_patrimonio( 
      id_baixapatrimonio  SERIAL    NOT NULL  , 
      cd_baixapatrimonio varchar  (10)   NOT NULL  , 
      dt_baixapatrimonio date   NOT NULL  , 
      vl_total float   NOT NULL  , 
      ds_observacao varchar  (1000)   NOT NULL  , 
 PRIMARY KEY (id_baixapatrimonio)) ; 

CREATE TABLE endereco( 
      id_endereco  SERIAL    NOT NULL  , 
      nu_cep varchar  (8)   NOT NULL  , 
      ds_logradouro varchar  (50)   NOT NULL  , 
      nm_bairro varchar  (50)   NOT NULL  , 
      nu_endereco varchar  (20)   , 
      ds_complemento varchar  (50)   , 
      nm_cidade varchar  (120)   NOT NULL  , 
      sg_uf integer   NOT NULL  , 
      id_fichacadastral integer   NOT NULL  , 
 PRIMARY KEY (id_endereco)) ; 

CREATE TABLE endereco_setor( 
      id_endereco  SERIAL    NOT NULL  , 
      nu_cep varchar  (8)   NOT NULL  , 
      ds_logradouro varchar  (50)   NOT NULL  , 
      nm_bairro varchar  (50)   NOT NULL  , 
      nu_endereco varchar  (20)   , 
      ds_complemento varchar  (50)   , 
      sg_uf integer   NOT NULL  , 
      nm_cidade varchar  (120)   NOT NULL  , 
      id_setor integer   NOT NULL  , 
 PRIMARY KEY (id_endereco)) ; 

CREATE TABLE ficha_cadastral( 
      id_fichacadastral  SERIAL    NOT NULL  , 
      tp_fichacadastral integer   NOT NULL  , 
      nm_fichacadastral varchar  (200)   NOT NULL  , 
      nu_cpfcnpj varchar  (14)   NOT NULL  , 
      ds_email varchar  (150)   NOT NULL  , 
      nu_telefone varchar  (11)   , 
      nm_fantasia varchar  (150)   , 
 PRIMARY KEY (id_fichacadastral)) ; 

CREATE TABLE grupo_bem( 
      id_grupobem  SERIAL    NOT NULL  , 
      cd_grupobem varchar  (10)   NOT NULL  , 
      nm_grupobem varchar  (50)   NOT NULL  , 
      ds_grupobem varchar  (1000)   NOT NULL  , 
 PRIMARY KEY (id_grupobem)) ; 

CREATE TABLE item_baixa( 
      id_itembaixa  SERIAL    NOT NULL  , 
      id_baixapatrimonio integer   NOT NULL  , 
      id_patrimonio integer   NOT NULL  , 
      vl_itembaixa float   NOT NULL  , 
 PRIMARY KEY (id_itembaixa)) ; 

CREATE TABLE item_bem( 
      id_itembem  SERIAL    NOT NULL  , 
      cd_itembem varchar  (10)   NOT NULL  , 
      nm_itembem varchar  (150)   NOT NULL  , 
      nm_unidademedida varchar  (50)   NOT NULL  , 
      id_grupobem integer   NOT NULL  , 
 PRIMARY KEY (id_itembem)) ; 

CREATE TABLE item_transferencia( 
      id_itemtransferencia  SERIAL    NOT NULL  , 
      vl_itemtransferencia float   NOT NULL  , 
      id_transferenciapatrimonio integer   NOT NULL  , 
      id_patrimonio integer   NOT NULL  , 
      id_setordestino integer   NOT NULL  , 
 PRIMARY KEY (id_itemtransferencia)) ; 

CREATE TABLE nota_fiscal( 
      id_notafiscal  SERIAL    NOT NULL  , 
      nu_chavenf varchar  (44)   NOT NULL  , 
      sg_uf char  (2)   NOT NULL  , 
      dt_emissao date   NOT NULL  , 
      nu_serie varchar  (4)   NOT NULL  , 
      id_fichacadastral integer   NOT NULL  , 
 PRIMARY KEY (id_notafiscal)) ; 

CREATE TABLE patrimonio( 
      id_patrimonio  SERIAL    NOT NULL  , 
      nu_plaqueta varchar  (10)   NOT NULL  , 
      dt_aquisicao date   NOT NULL  , 
      tp_entrada integer   NOT NULL  , 
      vl_bem float   NOT NULL  , 
      tp_situacao int   NOT NULL  , 
      ds_observacao varchar  (1000)   NOT NULL  , 
      id_setor integer   NOT NULL  , 
      id_itembem integer   NOT NULL  , 
      id_notafiscal integer   , 
 PRIMARY KEY (id_patrimonio)) ; 

CREATE TABLE setor( 
      id_setor  SERIAL    NOT NULL  , 
      cd_setor varchar  (10)   NOT NULL  , 
      nm_setor varchar  (50)   NOT NULL  , 
      id_fichacadastral integer   NOT NULL  , 
 PRIMARY KEY (id_setor)) ; 

CREATE TABLE transferencia_patrimonio( 
      id_transferenciapatrimonio  SERIAL    NOT NULL  , 
      cd_transferenciapatrimonio varchar  (10)   NOT NULL  , 
      dt_transferenciapatrimonio date   NOT NULL  , 
      vl_total float   NOT NULL  , 
      id_setororigem integer   NOT NULL  , 
 PRIMARY KEY (id_transferenciapatrimonio)) ; 

 
  
 ALTER TABLE endereco ADD CONSTRAINT fk_endereco_ficha_cadastral FOREIGN KEY (id_fichacadastral) references ficha_cadastral(id_fichacadastral); 
ALTER TABLE endereco_setor ADD CONSTRAINT fk_endereco_setor_setor FOREIGN KEY (id_setor) references setor(id_setor); 
ALTER TABLE item_baixa ADD CONSTRAINT fk_item_baixa_baixa_patrimonio FOREIGN KEY (id_baixapatrimonio) references baixa_patrimonio(id_baixapatrimonio); 
ALTER TABLE item_baixa ADD CONSTRAINT fk_item_baixa_patrimonio FOREIGN KEY (id_patrimonio) references patrimonio(id_patrimonio); 
ALTER TABLE item_bem ADD CONSTRAINT fk_item_bem_grupo_bem FOREIGN KEY (id_grupobem) references grupo_bem(id_grupobem); 
ALTER TABLE item_transferencia ADD CONSTRAINT fk_item_transferencia_transferencia_patrimonio FOREIGN KEY (id_transferenciapatrimonio) references transferencia_patrimonio(id_transferenciapatrimonio); 
ALTER TABLE item_transferencia ADD CONSTRAINT fk_item_transferencia_patrimonio FOREIGN KEY (id_patrimonio) references patrimonio(id_patrimonio); 
ALTER TABLE item_transferencia ADD CONSTRAINT fk_item_transferencia_setor FOREIGN KEY (id_setordestino) references setor(id_setor); 
ALTER TABLE nota_fiscal ADD CONSTRAINT fk_nota_fiscal_ficha_cadastral FOREIGN KEY (id_fichacadastral) references ficha_cadastral(id_fichacadastral); 
ALTER TABLE patrimonio ADD CONSTRAINT fk_patrimonio_setor FOREIGN KEY (id_setor) references setor(id_setor); 
ALTER TABLE patrimonio ADD CONSTRAINT fk_patrimonio_item_bem FOREIGN KEY (id_itembem) references item_bem(id_itembem); 
ALTER TABLE patrimonio ADD CONSTRAINT fk_patrimonio_nota_fiscal FOREIGN KEY (id_notafiscal) references nota_fiscal(id_notafiscal); 
ALTER TABLE setor ADD CONSTRAINT fk_setor_ficha_cadastral FOREIGN KEY (id_fichacadastral) references ficha_cadastral(id_fichacadastral); 
ALTER TABLE transferencia_patrimonio ADD CONSTRAINT fk_transferencia_patrimonio_setor FOREIGN KEY (id_setororigem) references setor(id_setor); 
SELECT setval('baixa_patrimonio_id_baixapatrimonio_seq', coalesce(max(id_baixapatrimonio),0) + 1, false) FROM baixa_patrimonio;
SELECT setval('endereco_id_endereco_seq', coalesce(max(id_endereco),0) + 1, false) FROM endereco;
SELECT setval('endereco_setor_id_endereco_seq', coalesce(max(id_endereco),0) + 1, false) FROM endereco_setor;
SELECT setval('ficha_cadastral_id_fichacadastral_seq', coalesce(max(id_fichacadastral),0) + 1, false) FROM ficha_cadastral;
SELECT setval('grupo_bem_id_grupobem_seq', coalesce(max(id_grupobem),0) + 1, false) FROM grupo_bem;
SELECT setval('item_baixa_id_itembaixa_seq', coalesce(max(id_itembaixa),0) + 1, false) FROM item_baixa;
SELECT setval('item_bem_id_itembem_seq', coalesce(max(id_itembem),0) + 1, false) FROM item_bem;
SELECT setval('item_transferencia_id_itemtransferencia_seq', coalesce(max(id_itemtransferencia),0) + 1, false) FROM item_transferencia;
SELECT setval('nota_fiscal_id_notafiscal_seq', coalesce(max(id_notafiscal),0) + 1, false) FROM nota_fiscal;
SELECT setval('patrimonio_id_patrimonio_seq', coalesce(max(id_patrimonio),0) + 1, false) FROM patrimonio;
SELECT setval('setor_id_setor_seq', coalesce(max(id_setor),0) + 1, false) FROM setor;
SELECT setval('transferencia_patrimonio_id_transferenciapatrimonio_seq', coalesce(max(id_transferenciapatrimonio),0) + 1, false) FROM transferencia_patrimonio;