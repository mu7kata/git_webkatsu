<if(!empty($_POST)){
    $attackFlg = (!empty($_POST['attack'])) ? true : false;
    $nextFlg = (!empty($_POST['next'])) ? true : false;
    $startFlg = (!empty($_POST['start'])) ? true : false;
    $next='off' ;
    error_log('POSTされた！');

    if($startFlg){
        History::set('ゲームスタート！');
        init();
    }else{
        // 攻撃するを押した場合
        if($attackFlg){
            // モンスターに攻撃を与える
            History::set($_SESSION['human']->getName().'の攻撃！');
            $_SESSION['human']->attack($_SESSION['monster']);
            $_SESSION['monster']->sayCry();

            // モンスターが攻撃をする
            History::set($_SESSION['monster']->getName().'の攻撃！');
            $_SESSION['monster']->attack($_SESSION['human']);
            $_SESSION['human']->sayCry();
            $next='on' ;

            // 自分のhpが0以下になったらゲームオーバー
            if($_SESSION['human']->getHp() <= 0){
                gameOver();
            }else{
                // hpが0以下になったら、別のモンスターを出現させる
                if($_SESSION['monster']->getHp() <= 0){
                    History::set($_SESSION['monster']->getName().'を倒した！');
                    createMonster();
                    $_SESSION['knockDownCount'] = $_SESSION['knockDownCount']+1;
                }
            }
        }
        elseif($nextFlg){
            History::set($_SESSION['monster']->getName().'の攻撃！');
            $_SESSION['monster']->attack($_SESSION['human']);
            $_SESSION['human']->sayCry();

        }else{ //逃げるを押した場合
            History::set('逃げた！');
            createMonster();
        }
    }
    $_POST = array();
}





?>
<?php



?>

<!DOCTYPE html>