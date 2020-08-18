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
$monsters = array();
// 性別クラス
class Sex{
    const MAN = 1;
    const WOMAN = 2;
    const OKAMA = 3;
}
// 抽象クラス（生き物クラス）
abstract class Creature{
    protected $name;
    protected $hp;
    protected $attackMin;
    protected $attackMax;
    //設計図名Creatureの中に名前、hp、最小最大攻撃力のプロパティを作成

    abstract public function sayCry();
    //sayCry()コンスタンスを挿入

    public function setName($str){
        $this->name = $str;
    }//setName()内の'name'要素に＄str変数の中身をを代入

    public function getName(){
        return $this->name;
    }//getName内の'name'要素の中身を返す。

    public function setHp($num){
        $this->hp = $num;
    }//setHp()内の'hp'要素に＄num変数の中身をを代入

    public function getHp(){
        return $this->hp;
    }//getHp内の'hp'要素の中身を返す。

    public function attack($targetObj){
        $attackPoint = mt_rand($this->attackMin, $this->attackMax);
        //$attackPointにランダムに代入

           if(!mt_rand(0,9)){ //10分の1の確率でクリティカル
                //mt_randの中身が０(false)だった場合
            $attackPoint = $attackPoint * 1.5;//攻撃力を1.5倍にする
            $attackPoint = (int)$attackPoint;// 数値にする
            History::set($this->getName().'のクリティカルヒット!!');
        //Historyにクリティカルヒットされた履歴を代入して残す。
        }
        $targetObj->setHp($targetObj->getHp()-$attackPoint);
        //攻撃対象のhpは　＝（”攻撃対象のhp” - アタックポイント）となる。
        History::set($attackPoint.'ポイントのダメージ！');
        //Historyにダメージ数の履歴を代入して残す。
    }
}
// 人クラス
class Human extends Creature{//”人”クラス作成 "生き物"クラスを継承（名前、hp、最小最大攻撃力のプロパティ）
    protected $sex;//性別情報を追加
    public function __construct($name, $sex, $hp, $attackMin, $attackMax) {
        $this->name = $name;
        $this->sex = $sex;
        $this->hp = $hp;
        $this->attackMin = $attackMin;
        $this->attackMax = $attackMax;
    }//インスタンス情報を代入するメソッド（コンスタンス）を作成
    
    public function setSex($num){
        $this->sex = $num;
    }//性別を取得する
    
    public function getSex(){
        return $this->sex;
    }//性別情報を返す。
    
    public function sayCry(){ //泣き叫ぶメソッドを作成
        History::set($this->name.'が叫ぶ！');
        // 履歴を残す。静的（スタティック）メンバを使用して。
        switch($this->sex){
            case Sex::MAN :
                History::set('ぐはぁっ！');
                break;
            case Sex::WOMAN :
                History::set('きゃっ！');
                break;
            case Sex::OKAMA :
                History::set('もっと！♡');
                break;
        }//性別によって結果を変更する。
    }
}
// モンスタークラス
class Monster extends Creature{//生き物"クラス名前、hp、最小最大攻撃力のプロパティ）を継承した”モンスター”クラスを作成
    
    protected $img;//imgプロパティを追加
    // コンストラクタ
    public function __construct($name, $hp, $img, $attackMin, $attackMax) {
        $this->name = $name;
        $this->hp = $hp;
        $this->img = $img;
        $this->attackMin = $attackMin;
        $this->attackMax = $attackMax;
    }//インスタンス情報を代入するメソッド（コンスタンス）を作成
    
    public function getImg(){
        return $this->img;
    }//img情報を返す
    public function sayCry(){//泣き叫ぶメソッドを作成
        History::set($this->name.'が叫ぶ！');
        History::set('はうっ！');
    }//二つ存在してrけど大丈夫なの？？
}
// 魔法を使えるモンスタークラス
class MagicMonster extends Monster{//モンスタークラスを継承したマジックモンスタークラスを作成
    private $magicAttack;//マジックアタックプロパティを追加
    function __construct($name, $hp, $img, $attackMin, $attackMax, $magicAttack) {
        parent::__construct($name, $hp, $img, $attackMin, $attackMax);
        いんすた
        $this->magicAttack = $magicAttack;
    }//インスタンス情報を代入するメソッド（コンスタンス）を作成（親のをパクる）オーバーライド
    
    public function getMagicAttack(){
        return $this->magicAttack;
    }//インスタンスのマジックアタック情報を返す
    
    public function attack($targetObj){//攻撃力を決めるメソッド（コンスタンス）
        if(!mt_rand(0,4)){ //5分の1の確率で魔法攻撃
            History::set($this->name.'の魔法攻撃!!');
            $targetObj->setHp( $targetObj->getHp() - $this->magicAttack ); //targetのhpを算出
            History::set($this->magicAttack.'ポイントのダメージを受けた！');
        }else{
            parent::attack($targetObj);
        }文クラスのattackメソッドをオーバーライド
    }
}
interface HistoryInterface{//共通の名前だけど、処理が違うもものinterface化
    public function set();
    public function clear();
}
// 履歴管理クラス（インスタンス化して複数に増殖させる必要性がないクラスなので、staticにする）
class History implements HistoryInterface{ in
    public static function set($str){
        // セッションhistoryが作られてなければ作る
        if(empty($_SESSION['history'])) $_SESSION['history'] = '';
        // 文字列をセッションhistoryへ格納
        $_SESSION['history'] .= $str.'<br>';
}// セッションhistoryが作られてなければ作るメソッド
    public static function clear(){
        unset($_SESSION['history']);
    }
}

// インスタンス生成
$human = new Human('勇者見習い', Sex::OKAMA, 500, 40, 120);
$monsters[] = new Monster( 'フランケン', 100, 'img/monster01.png', 20, 40 );
$monsters[] = new MagicMonster( 'フランケンNEO', 300, 'img/monster02.png', 20, 60, mt_rand(50, 100) );
$monsters[] = new Monster( 'ドラキュリー', 200, 'img/monster03.png', 30, 50 );
$monsters[] = new MagicMonster( 'ドラキュラ男爵', 400, 'img/monster04.png', 50, 80, mt_rand(60, 120) );
$monsters[] = new Monster( 'スカルフェイス', 150, 'img/monster05.png', 30, 60 );
$monsters[] = new Monster( '毒ハンド', 100, 'img/monster06.png', 10, 30 );
$monsters[] = new Monster( '泥ハンド', 120, 'img/monster07.png', 20, 30 );
$monsters[] = new Monster( '血のハンド', 180, 'img/monster08.png', 30, 50 );

function createMonster(){
    global $monsters;
    $monster =  $monsters[mt_rand(0, 7)];
    History::set($monster->getName().'が現れた！');
    $_SESSION['monster'] =  $monster;
}
function createHuman(){
    global $human;
    $_SESSION['human'] =  $human;
}
function init(){
    History::clear();
    History::set('初期化します！');
    $_SESSION['knockDownCount'] = 0;
    createHuman();
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
        }else{ //逃げるを押した場合
            History::set('逃げた！');
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
            <h2><?php echo $_SESSION['monster']->getName().'が現れた!!'; ?></h2>
            <div style="height: 150px;">
                <img src="<?php echo $_SESSION['monster']->getImg(); ?>" style="width:120px; height:auto; margin:40px auto 0 auto; display:block;">
            </div>
            <p style="font-size:14px; text-align:center;">モンスターのHP：<?php echo $_SESSION['monster']->getHp(); ?></p>
            <p>倒したモンスター数：<?php echo $_SESSION['knockDownCount']; ?></p>
            <p>勇者の残りHP：<?php echo $_SESSION['human']->getHp(); ?></p>
            <form method="post">
                <input type="submit" name="attack" value="▶攻撃する">
                <input type="submit" name="escape" value="▶逃げる">
                <input type="submit" name="start" value="▶ゲームリスタート">
            </form>
            <?php } ?>
            <div style="position:absolute; right:-350px; top:0; color:black; width: 300px;">
                <p><?php echo (!empty($_SESSION['history'])) ? $_SESSION['history'] : ''; ?></p>
            </div>
        </div>

    </body>
</html>
