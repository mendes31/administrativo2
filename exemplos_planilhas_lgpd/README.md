# 📊 Exemplos de Planilhas LGPD
## Modelos para Importação de Dados

Este diretório contém exemplos de planilhas CSV para facilitar o cadastro inicial de dados no sistema LGPD.

---

## 📁 **Arquivos Disponíveis**

### **1. `inventario_dados.csv`**
**Propósito**: Cadastro do inventário de dados pessoais
**Campos obrigatórios**:
- `area`: Área/Departamento (ex: Vendas, RH, Marketing)
- `data_type`: Tipo de dado (ex: Nome, CPF, Salário)
- `data_category`: Categoria (Pessoal/Sensível)
- `data_subject`: Titular dos dados (ex: Clientes, Funcionários)
- `storage_location`: Local de armazenamento
- `access_level`: Quem tem acesso
- `risk_level`: Nível de risco (Alto/Médio/Baixo)

### **2. `ropa.csv`**
**Propósito**: Registro de Operações de Tratamento (ROPA)
**Campos obrigatórios**:
- `finalidade`: Finalidade do processamento
- `titular`: Titulares dos dados
- `dados_tratados`: Dados tratados
- `base_legal`: Base legal do tratamento
- `compartilhamento`: Compartilhamento com terceiros
- `retencao`: Prazo de retenção
- `medidas_seguranca`: Medidas de segurança

### **3. `data_mapping.csv`**
**Propósito**: Mapeamento técnico de fluxo de dados
**Campos obrigatórios**:
- `source_system`: Sistema de origem
- `source_field`: Campo origem
- `transformation_rule`: Regra de transformação
- `destination_system`: Sistema destino
- `destination_field`: Campo destino
- `observation`: Observações (LGPD)

---

## 🔧 **Como Usar**

### **1. Preparação dos Dados**
1. Abra o arquivo CSV desejado em um editor de planilhas (Excel, Google Sheets, etc.)
2. Substitua os dados de exemplo pelos dados reais da sua organização
3. Mantenha o cabeçalho (primeira linha) inalterado
4. Salve como arquivo CSV com separador `;` (ponto e vírgula)

### **2. Importação no Sistema**
1. Acesse o módulo LGPD correspondente no sistema
2. Clique em "Importar Planilha" ou "Upload CSV"
3. Selecione o arquivo CSV preparado
4. Confirme a importação
5. Verifique os dados importados

### **3. Validação dos Dados**
Após a importação, verifique:
- ✅ Todos os registros foram importados corretamente
- ✅ Os relacionamentos entre as tabelas estão corretos
- ✅ Os níveis de risco estão adequados
- ✅ As medidas de segurança estão documentadas

---

## 📋 **Exemplos de Dados**

### **Inventário de Dados**
| Área | Tipo de Dado | Categoria | Titular | Local Armazenamento | Quem Tem Acesso | Risco |
|------|--------------|-----------|---------|-------------------|-----------------|-------|
| Vendas | Nome, CPF | Pessoal | Clientes | ERP - Servidor local | Vendas, Financeiro | Médio |
| RH | Dados bancários | Sensível | Funcionários | RH - Servidor interno | RH, Contabilidade ext. | Alto |

### **ROPA**
| Finalidade | Titular | Dados Tratados | Base Legal | Compartilhamento | Retenção | Medidas de Segurança |
|------------|---------|----------------|------------|------------------|----------|---------------------|
| Emissão de NF | Clientes | Nome, CPF, Telefone | Execução de contrato | Não há | 5 anos | Criptografia, acesso restrito |
| Pagamento de salários | Funcionários | Nome, CPF, Dados bancários | Obrigação legal | Contabilidade externa | 10 anos | Backup seguro, senhas fortes |

### **Data Mapping**
| Sistema Origem | Campo Origem | Regra Transformação | Sistema Destino | Campo Destino | Observação |
|----------------|--------------|-------------------|-----------------|---------------|------------|
| ERP (Vendas) | cpf | Remover pontos e traços | Financeiro | document_id | Dado sensível |
| E-commerce | E-mail | Validar duplicidade | Mailchimp | contact_email | Consentimento necessário |

---

## ⚠️ **Importante**

### **Formato do Arquivo**
- **Separador**: Use `;` (ponto e vírgula) como separador
- **Encoding**: UTF-8
- **Cabeçalho**: Mantenha a primeira linha com os nomes dos campos
- **Dados**: Não deixe linhas vazias no meio do arquivo

### **Validações**
- **Níveis de Risco**: Apenas "Alto", "Médio" ou "Baixo"
- **Categorias**: Apenas "Pessoal" ou "Sensível"
- **Campos Obrigatórios**: Todos os campos devem ser preenchidos
- **Caracteres Especiais**: Evite caracteres especiais nos nomes dos campos

### **Backup**
- Sempre faça backup dos dados antes da importação
- Teste a importação com poucos registros primeiro
- Verifique os logs de importação para identificar erros

---

## 🆘 **Suporte**

Em caso de dúvidas ou problemas com a importação:

1. **Verifique o formato**: Confirme se o arquivo está no formato correto
2. **Logs de erro**: Consulte os logs do sistema para identificar problemas
3. **Dados de exemplo**: Use os dados de exemplo como referência
4. **Suporte técnico**: Entre em contato com a equipe de TI

---

## 📈 **Próximos Passos**

Após a importação bem-sucedida:

1. **Revisar dados**: Verifique se todos os dados foram importados corretamente
2. **Relacionar registros**: Conecte os registros do inventário com ROPA e Data Mapping
3. **Gerar relatórios**: Use o sistema para gerar relatórios de conformidade
4. **Auditoria**: Realize auditoria dos dados importados
5. **Treinamento**: Capacite a equipe no uso do sistema

---

**🎯 Objetivo**: Facilitar o cadastro inicial de dados LGPD de forma estruturada e padronizada, garantindo conformidade com a legislação brasileira.