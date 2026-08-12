<?php

namespace App\Services\Financeiro;

/**
 * Parser de extrato bancário OFX (Open Financial Exchange) - formato padrão
 * que praticamente todo banco brasileiro exporta. Muitos bancos emitem OFX
 * em SGML "solto" (sem fechar toda tag), não XML estrito, então o parser é
 * por regex em cima do bloco <STMTTRN>...</STMTTRN> em vez de um parser XML,
 * que quebraria em arquivos reais.
 */
class OfxParser
{
    /** @return array<int, array{data: \DateTimeImmutable, descricao: string, valor: float, tipo: string}> */
    public function parse(string $conteudoOfx): array
    {
        $lancamentos = [];

        preg_match_all('/<STMTTRN>(.*?)<\/STMTTRN>/is', $conteudoOfx, $blocos);

        foreach ($blocos[1] as $bloco) {
            $data = $this->extrairTag($bloco, 'DTPOSTED');
            $valor = $this->extrairTag($bloco, 'TRNAMT');
            $memo = $this->extrairTag($bloco, 'MEMO') ?: $this->extrairTag($bloco, 'NAME');

            if ($data === null || $valor === null) {
                continue;
            }

            $valorFloat = (float) $valor;

            $lancamentos[] = [
                'data' => $this->parseData($data),
                'descricao' => trim($memo ?? 'Sem descrição'),
                'valor' => abs($valorFloat),
                'tipo' => $valorFloat >= 0 ? 'credito' : 'debito',
            ];
        }

        return $lancamentos;
    }

    protected function extrairTag(string $bloco, string $tag): ?string
    {
        // OFX em SGML não fecha toda tag - captura até a próxima tag (<) ou
        // quebra de linha, o que vier primeiro.
        if (preg_match('/<'.$tag.'>([^<\r\n]*)/i', $bloco, $m)) {
            return trim($m[1]);
        }

        return null;
    }

    protected function parseData(string $dataOfx): \DateTimeImmutable
    {
        // Formato OFX: YYYYMMDDHHMMSS ou só YYYYMMDD, às vezes com [-3:GMT] no fim.
        $digitos = substr(preg_replace('/\[.*?\]/', '', $dataOfx), 0, 8);

        return \DateTimeImmutable::createFromFormat('Ymd', $digitos) ?: new \DateTimeImmutable;
    }
}
