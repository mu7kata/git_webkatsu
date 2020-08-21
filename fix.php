<?php

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
ini_set("display_errors", "On");
// モンスター達格納用
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
    abstract public function sayCry();
    public function setName($str){
        $this->name = $str;
    }
    public function getName(){
        return $this->name;
    }
    public function setHp($num){
        $this->hp = $num;
    }
    public function getHp(){
        return $this->hp;
    }
    public function attack($targetObj){
        $attackPoint = mt_rand($this->attackMin, $this->attackMax);
        if(!mt_rand(0,9)){ //10分の1の確率でクリティカル
            $attackPoint = $attackPoint * 1.5;
            $attackPoint = (int)$attackPoint;
            History::set($this->getName().'のクリティカルヒット!!');
            condition::set($this->getName().'のクリティカルヒット!!');
        }
        $targetObj->setHp($targetObj->getHp()-$attackPoint);
        History::set($attackPoint.'ポイントのダメージ！');
        condition::set($attackPoint.'ポイントのダメージ！');
    }
}
// 人クラス
class Human extends Creature{
    protected $sex;
    public function __construct($name, $sex, $hp, $attackMin, $attackMax) {
        $this->name = $name;
        $this->sex = $sex;
        $this->hp = $hp;
        $this->attackMin = $attackMin;
        $this->attackMax = $attackMax;
    }
    public function setSex($num){
        $this->sex = $num;
    }
    public function getSex(){
        return $this->sex;
    }
    public function sayCry(){
        conditions::clear();
        History::set($this->name.'が叫ぶ！');
  
        switch($this->sex){
            case Sex::MAN :
                History::set('ぐはぁっ！');
                conditions::set('ぐは！♡');
                break;
            case Sex::WOMAN :
                History::set('きゃっ！');
                conditions::set('ぐは！♡');
                break;
            case Sex::OKAMA :
                History::set('もっと！♡');
                conditions::set(' \\\ もっと！♡ // ');
                break;
        }
    }
}
// モンスタークラス
class Monster extends Creature{
    // プロパティ
    protected $img;
    // コンストラクタ
    public function __construct($name, $hp, $img, $attackMin, $attackMax) {
        $this->name = $name;
        $this->hp = $hp;
        $this->img = $img;
        $this->attackMin = $attackMin;
        $this->attackMax = $attackMax;
    }
    // ゲッター
    public function getImg(){
        return $this->img;
    }
    public function sayCry(){
        History::set($this->name.'が叫ぶ！');
        History::set('はうっ！');
        conditions::clear();
        conditions::set('// はうっ！ \\\\');
    }
}
// 魔法を使えるモンスタークラス
class MagicMonster extends Monster{
    private $magicAttack;
    function __construct($name, $hp, $img, $attackMin, $attackMax, $magicAttack) {
        parent::__construct($name, $hp, $img, $attackMin, $attackMax);
        $this->magicAttack = $magicAttack;
    }
    public function getMagicAttack(){
        return $this->magicAttack;
    }
    public function attack($targetObj){
        if(!mt_rand(0,4)){ //5分の1の確率で魔法攻撃
            History::set($this->name.'の魔法攻撃!!');
            condition::set($this->name.'の魔法攻撃!!');
            $targetObj->setHp( $targetObj->getHp() - $this->magicAttack );
            History::set($this->magicAttack.'ポイントのダメージを受けた！');
            condition::set($this->magicAttack.'ポイントのダメージを受けた！');
        }else{
            parent::attack($targetObj);
        }
    }
}
interface HistoryInterface{
    public static function set($str);
    public static function clear();
}
// 履歴管理クラス（インスタンス化して複数に増殖させる必要性がないクラスなので、staticにする）
class History implements HistoryInterface{
    public static function set($str){
        // セッションhistoryが作られてなければ作る
        if(empty($_SESSION['history'])) $_SESSION['history'] = '';
        // 文字列をセッションhistoryへ格納
        $_SESSION['history'] .= $str.'<br>';
    }
    public static function clear(){
        unset($_SESSION['history']);
    }
}
interface conditionInterface{
    public static function set($str);
    public static function clear();
}

class condition implements conditionInterface {
public static function set($str){
    if(empty($_SESSION['condition'])) $_SESSION['condition'] = '';
    // 文字列をセッションhistoryへ格納
    $_SESSION['condition'] .= $str.'<br>';
}
    public static function clear(){
        unset($_SESSION['condition']);
    }
}
interface conditionsInterface{
    public static function set($str);
    public static function clear();
}

class conditions implements conditionInterface {
    public static function set($str){
        if(empty($_SESSION['conditions'])) $_SESSION['conditions'] = '';
        // 文字列をセッションhistoryへ格納
        $_SESSION['conditions'] .= $str.'<br>';
    }
    public static function clear(){
        unset($_SESSION['conditions']);
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
    condition::set($monster->getName().'が現れた！');
    $_SESSION['monster'] =  $monster;
    $_SESSION['monsterflg']=1;
}
function createHuman(){
    global $human;
    $_SESSION['human'] =  $human;
}
function init(){
    History::clear();
    condition::clear();
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
    $nextFlg = (!empty($_POST['next'])) ? true : false;
    $startFlg = (!empty($_POST['start'])) ? true : false;
    debug('$nextFlg：'.print_r($nextFlg,true));
    error_log('POSTされた！');
    

    
        
    if($startFlg){
        History::set('ゲームスタート！');
        init();
        $_SESSION['test'] =1;
}else{
    // 攻撃するを押した場合
    if($attackFlg){
        // モンスターに攻撃を与える
        debug('$attackFlgある？：'.print_r($attackFlg,true));
        History::set($_SESSION['human']->getName().'の攻撃！');
        condition::set($_SESSION['human']->getName().'の攻撃！');
        $_SESSION['human']->attack($_SESSION['monster']);
        $_SESSION['monster']->sayCry();
        $_SESSION['test'] = $_SESSION['test']+1;
        debug('$condition：'.print_r($_SESSION['condition'],true));
        // 自分のhpが0以下になったらゲームオーバー
        if($_SESSION['human']->getHp() <= 0){
            gameOver();   }
        
        
        else{
            // hpが0以下になったら、別のモンスターを出現させる
            if($_SESSION['monster']->getHp() <= 0){
    
                    condition::clear();
                $_SESSION['test'] = 3;
                History::set($_SESSION['human']->getName().'の攻撃！');
                condition::set($_SESSION['human']->getName().'の攻撃！');
                $_SESSION['human']->attack($_SESSION['monster']);
                $_SESSION['monster']->sayCry();
                $_SESSION['knockDownCount'] = $_SESSION['knockDownCount']+1;
                debug('$condition：'.print_r($_SESSION['condition'],true));
            }
            
        }
     }
        elseif($nextFlg){
            debug('$nextFlgある？：'.print_r($nextFlg,true));
//モンスターのHPが０だった場合
            if( $_SESSION['test']==3){
                History::set($_SESSION['monster']->getName().'を倒した！');
                condition::set($_SESSION['monster']->getName().'を倒した！');
                $_SESSION['knockDownCount'] = $_SESSION['knockDownCount']+1;
                debug('$condition：'.print_r($_SESSION['condition'],true));
                $_SESSION['test'] = 6;
            }else 
             if( $_SESSION['test']==6){
                createMonster();
                 debug('$condition2：'.print_r($_SESSION['condition'],true));
                $_SESSION['test']=1;
    
    }else{//モンスターのHPがあった場合
    History::set($_SESSION['monster']->getName().'の攻撃！');
    condition::clear();
    condition::set($_SESSION['monster']->getName().'の攻撃！');
    $_SESSION['monster']->attack($_SESSION['human']);
    $_SESSION['human']->sayCry();
    debug('$condition：'.print_r($_SESSION['condition'],true));
    $_SESSION['test'] = $_SESSION['test']+3;
             }
            
}else{ //逃げるを押した場合
    History::set('逃げた！');
    condition::set('逃げた！');
    createMonster();
}}
    if($_SESSION['test'] == 8){
        unset($_SESSION['test']);
        $_SESSION['test'] =1;
        unset($nextFlg) ;
            condition::clear();
     
    }
    debug('$testlast：'.print_r($_SESSION['test'],true));
   
   
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
                width: 56%;
                background: #fbfbfa;
                color: white;
            }
            h1{ color: white; font-size: 20px; text-align: center;}
            h2{ color: white; font-size: 16px; text-align: center;}
            form{
                overflow: hidden;
                border: 2px solid #fff;
                padding:10px;
             
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
            .p{
              height: 10px;
                position: relative;
                top: -3px;
                left: 317px;
                font-size: 24px;
                color: crimson;
            }
            .r{
                height: 10px;
                position: relative;
                top: 209px;
                left: 308px;
                font-size: 24px;
                color: crimson;
            }
            .message_p{

            height: 40px;
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
            <div style="height: 150px; position:relative;"  >
                <img src="<?php echo $_SESSION['monster']->getImg(); ?>" style="width:120px; height:auto; margin:40px auto 0 auto; display:block;">
                <?php if($attackFlg) { ?>
                <p class="p"><?php echo (!empty($_SESSION['conditions'])) ? $_SESSION['conditions'] : ''; ?></p>
                <?php } ; ?>
                <?php if( $_SESSION['test']==5) { ?>
                <p class="r"><?php echo (!empty($_SESSION['conditions'])) ? $_SESSION['conditions'] : ''; ?></p>
                <?php } ; ?>
            </div>
            <p style="font-size:14px; text-align:center;">モンスターのHP：<?php echo $_SESSION['monster']->getHp(); ?></p>
            <p>倒したモンスター数：<?php echo $_SESSION['knockDownCount']; ?></p>
            <p>勇者の残りHP：<?php echo $_SESSION['human']->getHp(); ?></p>
            
            <form method="post">

                <?php if($_SESSION['monsterflg']==1){ ?>
                <p class="message_p"><?php echo (!empty($_SESSION['condition'])) ? $_SESSION['condition'] : ''; ?></p>

                <?php }   condition::clear();  ?>
                
                <?php  if($_SESSION['test']==1 ){   ?>
                <p class="message_p"></p>
                <input type="submit" name="attack" value="▶攻撃する">
                <input type="submit" name="escape" value="▶逃げる">
                <?php } ?>
                
                <?php if( $_SESSION['test']==2 ){ ?>
                <p class="message_p"><?php echo (!empty($_SESSION['condition'])) ? $_SESSION['condition'] : ''; ?></p>
                <input type="submit" name="next" value="▶次へ">
                <?php }  ?>
                
                <?php if( $_SESSION['test']==3 ){ ?>
                <p class="message_p"><?php echo (!empty($_SESSION['condition'])) ? $_SESSION['condition'] : ''; ?></p>
                <input type="submit" name="next" value="▶次へ">
                <?php }  ?>

                <?php if( $_SESSION['test']==6 ){ ?>
                <p class="message_p"><?php echo (!empty($_SESSION['condition'])) ? $_SESSION['condition'] : ''; ?></p>
                <input type="submit" name="next" value="▶次へ">
                <?php }  ?>

                <?php if( $_SESSION['test']==8){ ?>
                <p class="message_p"><?php echo (!empty($_SESSION['condition'])) ? $_SESSION['condition'] : ''; ?></p>
                <input type="submit" name="next" value="▶次へ">
              
                <?php }  ?>
                <?php if( $_SESSION['test']==5){ ?>
                <p class="message_p"><?php echo (!empty($_SESSION['condition'])) ? $_SESSION['condition'] : ''; ?></p>
                <input type="submit" name="next" value="▶次へ">

                <?php }  ?>
                <input type="submit" name="start" value="▶ゲームリスタート">
                     
            </form>
           <?php }
            ?>
            
            <div style="position:absolute; right:-350px; top:0; color:black; width: 300px;">
                <p><?php echo (!empty($_SESSION['history'])) ? $_SESSION['history'] : ''; ?></p>
            </div>
        </div>

    </body>
</html>