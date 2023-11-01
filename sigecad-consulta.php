<?php

const DBCONN_ACADEMICOS_VIEW = "secretaria.disciplinas_academicos_para_moodle";
const DBCONN_CARTOES_VIEW = "secretaria.cartoes_para_sophia_moodle";
const DBCONN_PESSOAS_VIEW = "public.pessoas_view";

const DBCONN_KEYFIELD = "username";

const NOME_QUERY = "query_sigecad";

const COLUNA_NOME_ALUNO = "nome_aluno";
const COLUNA_NOME_PROFESSOR = "nome_professor";
const COLUNA_USERNAME_PROFESSOR = "username_professor";

const COLUNA_BUSCA_CARTAO = "cpf";
const COLUNA_NUMERO_CARTAO = "numero_cartao";
const COLUNA_ESTADO_CARTAO = "estado_cartao";
const COLUNA_ESTATUS_PESSOA_CARTAO = "tipo_estatus_nome";

const COLUNA_NOME_PESSOA = "nome";
const COLUNA_TIPO_ESTATUS_PESSOA = "tipo_estatus_nome";
const COLUNA_TIPO_PESSOA = "tipo_pessoa";
const COLUNA_LOTACAO_PESSOA = "lotacao";
const COLUNA_FACULDADE_PESSOA = "faculdade";


function cosultaCartao($documento)
{
    $result = executarGeneric(DBCONN_CARTOES_VIEW, [COLUNA_BUSCA_CARTAO => $documento]);
    if ($result) {
        return $result[0][COLUNA_NUMERO_CARTAO];
    }
    return '';
}


function executarGeneric($view, $parametros, $colunas = [], $nativeQuery = null)
{
    $dbconn = \pg_connect("host=" . getenv('DB_ACAD_HOSTNAME') .
        " port=" . getenv('DB_ACAD_PORT') .
        " dbname=" . getenv('DB_ACAD_DBNAME') .
        " user=" . getenv('DB_ACAD_USERNAME') .
        " password=" . getenv('DB_ACAD_PASSWORD'));


    if ($nativeQuery) {
        $result = pg_prepare($dbconn, NOME_QUERY, $nativeQuery);
    } else {
        $cols = "";
        $first = true;
        foreach ($colunas as $nomeCol => $rename) {
            $cols .= ($first ? ' ' : ', ') . $nomeCol . ' ' . $rename;
            $first = false;
        }
        if (!$cols)
            $cols = " *";
        if (is_array($parametros)) {
            if (count($parametros) > 0 && array_keys($parametros)[0] == '0') {
                $vars = "($1";
                for ($i = 2; $i <= count($parametros); $i++) {
                    $vars .= ",$" . $i;
                }
                $vars .= ")";
                $result = pg_prepare($dbconn, NOME_QUERY, 'SELECT' . $cols . ' FROM ' . $view . ' WHERE ' . DBCONN_KEYFIELD . ' IN ' . $vars);
            } else {
                $query = 'SELECT' . $cols . ' FROM ' . $view;
                $first = true;
                $index = 1;
                $parametrosReindex = [];
                foreach ($parametros as $field => $value) {
                    if ($field == COLUNA_USERNAME_PROFESSOR || $field == COLUNA_NOME_PROFESSOR)
                        $query .= ($first ? ' WHERE ' : ' AND ') . 'unaccent(' . $field . ') ilike unaccent($' . $index . ')';
                    elseif ($field == COLUNA_LOTACAO_PESSOA || $field == COLUNA_FACULDADE_PESSOA  || $field == COLUNA_TIPO_ESTATUS_PESSOA) {
                        if ($field == COLUNA_LOTACAO_PESSOA && $parametros[COLUNA_TIPO_PESSOA] == 'funcionario') {
                            $val1 = "%/" . $value . " - %";
                            $val2 = $value . " - %";
                            $query .= ($first ? ' WHERE ' : ' AND ') . '(TRIM(' . $field . ') ilike $' . $index . ' OR TRIM(' . $field . ') ilike $' . ($index + 1) . ')';
                            $parametrosReindex[] = $val1;
                            $index++;
                            $value = $val2;
                        } else
                            $query .= ($first ? ' WHERE ' : ' AND ') . 'TRIM(' . $field . ') = TRIM($' . $index . ')';
                    } else
                        $query .= ($first ? ' WHERE ' : ' AND ') . $field . ' =  $' . $index;
                    $parametrosReindex[] = $value;
                    $index++;
                    $first = false;
                }
                if ($view == DBCONN_ACADEMICOS_VIEW)
                    $query .= ' ORDER BY ' . COLUNA_NOME_ALUNO;
                elseif ($view == DBCONN_CARTOES_VIEW)
                    $query .= ' ORDER BY ' . COLUNA_ESTADO_CARTAO . ',' . COLUNA_ESTATUS_PESSOA_CARTAO;
                elseif ($view == DBCONN_PESSOAS_VIEW)
                    $query .= ' ORDER BY ' . COLUNA_NOME_PESSOA . ',' . COLUNA_TIPO_ESTATUS_PESSOA;
                //return $query;
                $result = pg_prepare($dbconn, NOME_QUERY, $query);
                $parametros = $parametrosReindex;
            }
        } else {
            $result = pg_prepare($dbconn, NOME_QUERY, 'SELECT' . $cols . ' FROM ' . $view . ' WHERE ' . DBCONN_KEYFIELD . ' = $1');
        }
    }

    $result = pg_execute($dbconn, NOME_QUERY, $parametros);

    $arr = pg_fetch_all($result);

    pg_close($dbconn);

    return $arr ? $arr : [];
}
