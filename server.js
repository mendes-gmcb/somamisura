const express = require('express');
const multer = require('multer');
const fs = require('fs');
const readline = require('readline');
const path = require('path');
const { createObjectCsvWriter } = require('csv-writer');

const app = express();
const port = 3000;

// Configurar o Multer para salvar arquivos enviados
const upload = multer({ dest: 'uploads/' });

// Função para somar os valores das linhas que começam com "mizura"
async function sumMizura(filePath) {
    const fileStream = fs.createReadStream(filePath);

    const rl = readline.createInterface({
        input: fileStream,
        crlfDelay: Infinity
    });

    let totalSum = 0; 
    let date = '';

    for await (const line of rl) {
        if (line.includes("[Misura in unita' di misura -]")) {
            const match = line.match(/\[Misura in unita' di misura -\]\s+([\d.]+)/);
            if (match) {
                const value = parseFloat(match[1]);
                if (!isNaN(value)) {
                    totalSum += value;
                }
            }
        }
        if (line.includes('#Data')) {
            const dateMatch = line.match(/#Data\s+#(\d{2}-\d{2}-\d{4})/);
            if (dateMatch) {
                date = dateMatch[1];
            }
        }
    }

    totalSum = totalSum.toFixed(2).replace('.', ',');

    return { totalSum, date };
}

// Configurar o servidor para servir os arquivos estáticos da pasta 'public'
app.use(express.static('public'));

// Endpoint para fazer upload dos arquivos
app.post('/upload', upload.array('files'), async (req, res) => {
    const files = req.files;
    const results = [];

    for (const file of files) {
        const { totalSum, date } = await sumMizura(file.path);
        results.push({ filename: file.originalname, date, sum: totalSum });
        fs.unlinkSync(file.path); // Exclui o arquivo original
    }

    // Criar CSV
    const csvWriter = createObjectCsvWriter({
        path: 'uploads/results.csv',
        header: [
            { id: 'filename', title: 'Arquivo' },
            { id: 'date', title: 'Data' },
            { id: 'sum', title: 'Soma' }
        ]
    });

    await csvWriter.writeRecords(results);

    res.download('uploads/results.csv', 'results.csv', (err) => {
        if (err) {
            console.error('Erro ao baixar o arquivo CSV:', err);
        } else {
            fs.unlinkSync('uploads/results.csv'); // Excluir o arquivo CSV após o download
        }
    });
});

app.listen(port, () => {
    console.log(`Server is running on http://localhost:${port}`);
});
