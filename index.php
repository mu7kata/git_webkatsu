<?php
//================================
// ログ
//================================
ini_set('log_errors','on');
//ログの出力ファイルを指定
ini_set('error_log','php.log');


//================================
// デバッグ
//================================
//デバッグフラグ
$debug_flg = true;
//デバッグログ関数
function debug($str){
    global $debug_flg;
    if(!empty($debug_flg)){
        error_log('デバッグ：'.$str);
    }
}


ini_set('log_errors','on');  //ログを取るか
ini_set('error_log','php.log');  //ログの出力ファイルを指定
session_start(); //セッション使う

// 自分のHP
define("MY_HP", 500);
// モンスター達格納用
$monsters = array();

class Monster{// クラス（設計図）の作成
    // プロパティ（属性）の作成（固定データ）
    public $name; // 定義しただけだとnullが入る
    public $hp;
    public $img;
    public $attack = ''; // nullを入れたくない場合、空文字などで初期化する
    // コンストラクタ（動き、メソッド）の作成
    public function __construct($name, $hp, $img, $attack) {//インスタンスのデータを代入するメソッドを作成
        $this->name = $name;
        $this->hp = $hp;
        $this->img = $img;
        $this->attack = $attack;
    }
    // メソッド
    public function attack(){
        $_SESSION['myhp'] -= $this->attack;
        $_SESSION['history'] .= $this->attack.'ポイントのダメージを受けた！<br>';
    }
}
// インスタンス生成
$monsters[] = new Monster( 'フランケン', 100, 'img/monster01.png', mt_rand(20, 40) );
$monsters[] = new Monster( 'フランケンNEO', 300, 'img/monster02.png', mt_rand(20, 60) );
$monsters[] = new Monster( 'ドラキュリー', 200, 'img/monster03.png', mt_rand(30, 50) );
$monsters[] = new Monster( 'ドラキュラ男爵', 400, 'img/monster04.png', mt_rand(50, 80) );
$monsters[] = new Monster( 'スカルフェイス', 150, 'img/monster05.png', mt_rand(30, 60) );
$monsters[] = new Monster( '毒ハンド', 100, 'img/monster06.png', mt_rand(10, 30) );
$monsters[] = new Monster( '泥ハンド', 120, 'img/monster07.png', mt_rand(20, 30) );
$monsters[] = new Monster( '血のハンド', 180, 'img/monster08.png', mt_rand(30, 50) );

function createMonster(){
    global $monsters;
    $monster =  $monsters[mt_rand(0, 6)];
    $_SESSION['history'] .= $monster->name.'が現れた！<br>';
    $_SESSION['monster'] =  $monster;
}
function init(){
    $_SESSION['history'] .= '初期化します！<br>';
    $_SESSION['knockDownCount'] = 0;
    $_SESSION['myhp'] = MY_HP;
    createMonster();
}
function gameOver(){
    $_SESSION = array();
}


//1.post送信されていた場合
if(!empty($_POST)){
    $attackFlg = (!empty($_POST['attack'])) ? true : false;
    $startFlg = (!empty($_POST['start'])) ? true : false;
    error_log('POSTされた！');

    if($startFlg){
        $_SESSION['history'] = 'ゲームスタート！<br>';
        init();//初期化＆createMonster();を発動
    }else{
        // 攻撃するを押した場合
        if($attackFlg){
            $_SESSION['history'] .= '攻撃した！<br>';

            // ランダムでモンスターに攻撃を与える
            $attackPoint = mt_rand(50,100);
            $_SESSION['monster']->hp -= mt_rand(50,100);
            $_SESSION['history'] .= $attackPoint.'ポイントのダメージを与えた！<br>';
            // モンスターから攻撃を受ける
            $_SESSION['monster']->attack();

            // 自分のhpが0以下になったらゲームオーバー
            if($_SESSION['myhp'] <= 0){
                gameOver();
            }else{
                // hpが0以下になったら、別のモンスターを出現させる
                if($_SESSION['monster']->hp <= 0){
                    $_SESSION['history'] .= $_SESSION['monster']->name.'を倒した！<br>';
                    createMonster();
                    $_SESSION['knockDownCount'] = $_SESSION['knockDownCount']+1;
                }
            }
        }else{ //逃げるを押した場合
            $_SESSION['history'] .= '逃げた！<br>';
            createMonster();
        }
    }
    $_POST = array();
}

?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>ホームページのタイトル</title>
        <style>
            body{
                margin: 0 auto;
                padding: 150px;
                width: 25%;
                background: #fbfbfa;
                color: white;
            }
            h1{ color: white; font-size: 20px; text-align: center;}
            h2{ color: white; font-size: 16px; text-align: center;}
            form{
                overflow: hidden;
            }
            input[type="text"]{
                color: #545454;
                height: 60px;
                width: 100%;
                padding: 5px 10px;
                font-size: 16px;
                display: block;
                margin-bottom: 10px;
                box-sizing: border-box;
            }
            input[type="password"]{
                color: #545454;
                height: 60px;
                width: 100%;
                padding: 5px 10px;
                font-size: 16px;
                display: block;
                margin-bottom: 10px;
                box-sizing: border-box;
            }
            input[type="submit"]{
                border: none;
                padding: 15px 30px;
                margin-bottom: 15px;
                background: black;
                color: white;
                float: right;
            }
            input[type="submit"]:hover{
                background: #3d3938;
                cursor: pointer;
            }
            a{
                color: #545454;
                display: block;
            }
            a:hover{
                text-decoration: none;
            }
        </style>
    </head>
    <body>
        <h1 style="text-align:center; color:#333;">ゲーム「ドラ◯エ!!」</h1>
        <div style="background:black; padding:15px; position:relative;">
            <?php if(empty($_SESSION)){ ?>
            <h2 style="margin-top:60px;">GAME START ?</h2>
            <form method="post">
                <input type="submit" name="start" value="▶ゲームスタート">
            </form>
            <?php }else{ ?>
            <h2><?php echo $_SESSION['monster']->name.'が現れた!!'; ?></h2>
            <div style="height: 150px;">
                <img src="<?php echo $_SESSION['monster']->img; ?>" style="width:120px; height:auto; margin:40px auto 0 auto; display:block;">
            </div>
            <p style="font-size:14px; text-align:center;">モンスターのHP：<?php echo $_SESSION['monster']->hp; ?></p>
            <p>倒したモンスター数：<?php echo $_SESSION['knockDownCount']; ?></p>
            <p>勇者の残りHP：<?php echo $_SESSION['myhp']; ?></p>
            <form method="post">
                <input type="submit" name="attack" value="▶攻撃する">
                <input type="submit" name="escape" value="▶逃げる">
                <input type="submit" name="start" value="▶ゲームリスタート">
            </form>
            <?php } ?>
            <div style="position:absolute; right:-300px; top:0; color:black; width: 250px;">
                <p><?php echo (!empty($_SESSION['history'])) ? $_SESSION['history'] : ''; ?></p>
            </div>
        </div>

    </body>
</html>