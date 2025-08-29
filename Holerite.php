<?php
session_start();

// Inicializa a lista de funcionários
if(!isset($_SESSION['funcionarios'])){
    $_SESSION['funcionarios'] = [];
}

// Função INSS progressivo
function calcularINSS($salario){
    $faixas = [
        [1518.00, 0.075],
        [2793.88, 0.09],
        [3646.83, 0.12],
        [8157.41, 0.14]
    ];
    $inss = 0;
    $limiteAnterior = 0;
    foreach($faixas as $faixa){
        $limite = $faixa[0];
        $aliquota = $faixa[1];
        if($salario > $limite){
            $inss += ($limite - $limiteAnterior) * $aliquota;
        } else {
            $inss += ($salario - $limiteAnterior) * $aliquota;
            break;
        }
        $limiteAnterior = $limite;
    }
    return $inss;
}

// Função IRRF progressivo
function calcularIRRF($salario){
    $faixas = [
        [2428.80,0],
        [2826.65,0.075],
        [3751.05,0.15],
        [4664.68,0.225],
        [999999.99,0.275]
    ];
    $irrf = 0;
    $limiteAnterior = 0;
    foreach($faixas as $faixa){
        $limite = $faixa[0];
        $aliquota = $faixa[1];
        if($salario > $limite){
            $irrf += ($limite - $limiteAnterior) * $aliquota;
        } else {
            $irrf += ($salario - $limiteAnterior) * $aliquota;
            break;
        }
        $limiteAnterior = $limite;
    }
    return $irrf;
}

// Adicionar funcionário
if(isset($_POST['adicionar'])){
    $nome = $_POST['nome'];
    $salario_bruto = floatval($_POST['salariobruto']);
    $vt = floatval($_POST['vt']);
    $vr = floatval($_POST['vr']);

    $inss = calcularINSS($salario_bruto);
    $irrf = calcularIRRF($salario_bruto);
    $salario_liquido = $salario_bruto - $inss - $irrf - $vt - $vr;

    $_SESSION['funcionarios'][] = [
        "nome"=>$nome,
        "salario_bruto"=>$salario_bruto,
        "vt"=>$vt,
        "vr"=>$vr,
        "inss"=>$inss,
        "irrf"=>$irrf,
        "salario_liquido"=>$salario_liquido
    ];

    // volta para index.html com mensagem
    header("Location: index.html?msg=ok");
    exit();
}

// Finalizar e exibir todos
if(isset($_POST['finalizar'])){
    if(empty($_SESSION['funcionarios'])){
        echo "<p>Nenhum funcionário cadastrado.</p>";
    } else {
        echo "<h2>Holerite de Funcionários</h2>";
        echo "<table border='1' cellpadding='8'>
                <tr>
                    <th>Nome</th>
                    <th>Salário Bruto</th>
                    <th>INSS</th>
                    <th>IRRF</th>
                    <th>VT</th>
                    <th>VR</th>
                    <th>Salário Líquido</th>
                </tr>";
        foreach($_SESSION['funcionarios'] as $f){
            echo "<tr>
                    <td>{$f['nome']}</td>
                    <td>R$ ".number_format($f['salario_bruto'],2,',','.')."</td>
                    <td>R$ ".number_format($f['inss'],2,',','.')."</td>
                    <td>R$ ".number_format($f['irrf'],2,',','.')."</td>
                    <td>R$ ".number_format($f['vt'],2,',','.')."</td>
                    <td>R$ ".number_format($f['vr'],2,',','.')."</td>
                    <td><b>R$ ".number_format($f['salario_liquido'],2,',','.')."</b></td>
                  </tr>";
        }
        echo "</table>";
        session_destroy();
    }
}
?>