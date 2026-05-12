<?php
$letters_en = [
    "A" => ["Apple",     "pictures/apple.jpg", "#FF6B6B"],
    "B" => ["Banana",    "pictures/bananas.jpg", "#FFD93D"],
    "C" => ["Cat",       "pictures/catt.jpg", "#FF8E53"],
    "D" => ["Dog",       "pictures/dogi.jpg", "#A0522D"],
    "E" => ["Elephant",  "pictures/elephantt.jpg", "#6B7B8D"],
    "F" => ["Frog",      "pictures/frogg.jpg", "#6BCB77"],
    "G" => ["Giraffe",   "pictures/giraffee.jpg", "#F9A825"],
    "H" => ["Horse",     "pictures/horsee.jpg", "#E57373"],
    "I" => ["Ice Cream", "pictures/ice creamm.jpg", "#FD79A8"],
    "J" => ["Jellyfish", "pictures/jellyfishh.jpg", "#845EC2"],
    "K" => ["Kangaroo",  "pictures/kangarooo.jpg", "#C0854A"],
    "L" => ["Lion",      "pictures/lionn.jpg", "#E6AC00"],
    "M" => ["Monkey",    "pictures/monkeyy.jpg", "#7B5EA7"],
    "N" => ["Nest",      "pictures/nestt.jpg", "#5C9E6B"],
    "O" => ["Octopus",   "pictures/octopuss.jpg", "#E53935"],
    "P" => ["Parrot",    "pictures/parrott.jpg", "#26A69A"],
    "Q" => ["Quail",     "pictures/quaill.jpg", "#795548"],
    "R" => ["Rabbit",    "pictures/rabbitt.jpg", "#F48FB1"],
    "S" => ["Sunflower", "pictures/sunflowerr.jpg", "#FDD835"],
    "T" => ["Tiger",     "pictures/tigerr.jpg", "#FF6F00"],
    "U" => ["Umbrella",  "pictures/umbrellaa.jpg", "#1E88E5"],
    "V" => ["Violin",    "pictures/violinn.jpg", "#AB47BC"],
    "W" => ["Whale",     "pictures/whalee.jpg", "#00ACC1"],
    "X" => ["Xylophone", "pictures/xylophonee.jpg", "#E91E8C"],
    "Y" => ["Yak",       "pictures/yakk.jpg", "#546E7A"],
    "Z" => ["Zebra",     "pictures/zebraa.jpg", "#424242"],
];

$letters_tl = [
    "A" => ["Aso",     "pictures/dogi.jpg", "#FF6B6B"],
    "B" => ["Bahay",   "pictures/bahay.jpg", "#FFD93D"],
    "D" => ["Daga",    "pictures/daga.jpg", "#A0522D"],
    "E" => ["Elepante","pictures/elephantt.jpg", "#6B7B8D"],
    "G" => ["Gatas",   "pictures/gatas.jpg", "#F9A825"],
    "H" => ["Hangin",  "pictures/hangin.jpg", "#E57373"],
    "I" => ["Ibon",    "pictures/parrott.jpg", "#FD79A8"],
    "K" => ["Kambing", "pictures/kambing.jpg", "#6BCB77"],
    "L" => ["Langit",  "pictures/langit.jpg", "#1E88E5"],
    "M" => ["Mangga",  "pictures/mangga.jpg", "#7B5EA7"],
    "N" => ["Niyog",   "pictures/niyog.jpg", "#5C9E6B"],
    "O" => ["Oso",     "pictures/oso.jpg", "#FF8E53"],
    "P" => ["Pato",    "pictures/pato.jpg", "#26A69A"],
    "R" => ["Relo",    "pictures/relo.jpg", "#F48FB1"],
    "S" => ["Saging",  "pictures/bananas.jpg", "#FDD835"],
    "T" => ["Tasa",    "pictures/tasa.jpg", "#FF6F00"],
    "U" => ["Uod",     "pictures/uod.jpg", "#845EC2"],
    "W" => ["Watawat", "pictures/watawat.jpg", "#00ACC1"],
    "Y" => ["Yelo",    "pictures/yelo.jpg", "#546E7A"],
];

$vowels_list = ['A','E','I','O','U'];
$vc_words_en = [
  'A'=>['ant','apple','arrow'],  'E'=>['egg','eagle','earth'],
  'I'=>['igloo','insect','ice'], 'O'=>['owl','orange','ocean'],
  'U'=>['umbrella','up','uncle'],'B'=>['ball','bear','bee'],
  'C'=>['cat','cake','cup'],     'D'=>['dog','drum','duck'],
  'F'=>['fish','fire','frog'],   'G'=>['goat','grape','gem'],
  'H'=>['hat','hen','hill'],     'J'=>['jar','jet','jam'],
  'K'=>['kite','king','key'],    'L'=>['lamp','lion','leaf'],
  'M'=>['map','moon','milk'],    'N'=>['nest','net','nose'],
  'P'=>['pig','pen','pin'],      'Q'=>['queen','quilt','quiz'],
  'R'=>['rose','rain','ring'],   'S'=>['sun','sock','snake'],
  'T'=>['top','tree','tail'],    'V'=>['van','vine','vest'],
  'W'=>['well','wind','wing'],   'X'=>['x-ray','xylophone','fox'],
  'Y'=>['yak','yarn','yell'],    'Z'=>['zoo','zero','zip'],
];
$vc_words_tl = [
  'A'=>['aso','araw','alon'],    'E'=>['elepante','eroplano','isda'],
  'I'=>['ibon','isda','init'],   'O'=>['oso','obra','oras'],
  'U'=>['uod','ulan','ulap'],    'B'=>['bahay','bato','bata'],
  'D'=>['daga','dahon','dagat'], 'G'=>['gatas','gulay','gubat'],
  'H'=>['hangin','hilaga','hali'],'K'=>['kambing','kalabaw','kawayan'],
  'L'=>['langit','lupa','laro'], 'M'=>['mangga','mata','manok'],
  'N'=>['niyog','noo','nais'],   'Ng'=>['ngiti','ngalan','ngayon'],
  'P'=>['pato','puso','puno'],   'R'=>['relo','ranas','riles'],
  'S'=>['saging','siko','sinag'],'T'=>['tasa','tao','tubig'],
  'W'=>['watawat','wika','wala'],'Y'=>['yelo','yaman','yaya'],
];
$alphabet_tl = ['A','B','D','E','G','H','I','K','L','M','N','Ng','O','P','R','S','T','U','W','Y'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Letters — E-KINDER</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Fredoka+One&family=Nunito:wght@400;600;700;800;900&display=swap" rel="stylesheet">
<style>
:root{
  --nav-h:64px;--cream:#fdf8f0;--pill:999px;
  --vowel:#e8336d;--vowel-light:#fff0f5;--vowel-dark:#a3184a;
  --cons:#1a6de8;--cons-light:#f0f5ff;--cons-dark:#1043a3;
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
body{font-family:'Nunito',sans-serif;background:var(--cream);min-height:100vh;overflow-x:hidden;}
body::before{content:'';position:fixed;inset:0;pointer-events:none;z-index:0;
  background-image:radial-gradient(circle,rgba(0,0,0,.038) 1.2px,transparent 1.2px);
  background-size:26px 26px;}
.page-wrap{position:relative;z-index:1;padding-bottom:80px;}

/* ---- SCREEN FLASH OVERLAY ---- */
#screenFlash{
  position:fixed;inset:0;z-index:9999;pointer-events:none;opacity:0;
  transition:opacity .08s ease;
}
#screenFlash.flash-correct{background:rgba(107,203,119,.45);animation:flashCorrect .55s ease forwards;}
#screenFlash.flash-wrong{background:rgba(255,107,107,.5);animation:flashWrong .55s ease forwards;}
@keyframes flashCorrect{0%{opacity:0}20%{opacity:1}100%{opacity:0}}
@keyframes flashWrong{
  0%{opacity:0}15%{opacity:1}30%{opacity:.7}45%{opacity:1}60%{opacity:.5}100%{opacity:0}
}

.lesson-nav{height:var(--nav-h);padding:0 28px;background:rgba(255,255,255,.94);
  backdrop-filter:blur(16px);border-bottom:1px solid rgba(0,0,0,.07);
  box-shadow:0 2px 18px rgba(0,0,0,.06);position:sticky;top:0;z-index:200;
  display:flex;align-items:center;gap:14px;}
.lnav-back{display:inline-flex;align-items:center;gap:8px;background:#1a2e1a;color:#fff;
  font-family:'Fredoka One',cursive;font-size:.88rem;padding:8px 20px;
  border-radius:var(--pill);text-decoration:none;flex-shrink:0;
  box-shadow:0 4px 14px rgba(0,0,0,.22);transition:transform .22s cubic-bezier(.34,1.56,.64,1),box-shadow .22s;}
.lnav-back:hover{transform:scale(1.06);box-shadow:0 7px 20px rgba(0,0,0,.3);color:#fff;}
.lnav-spacer{flex:1;}
.lang-toggle{display:inline-flex;align-items:center;background:#f0f0f0;
  border-radius:var(--pill);padding:4px;border:1.5px solid #e0e0e0;}
.lang-btn{font-family:'Fredoka One',cursive;font-size:.8rem;padding:6px 18px;
  border-radius:var(--pill);border:none;cursor:pointer;background:transparent;color:#aaa;
  display:flex;align-items:center;gap:6px;
  transition:background .2s,color .2s,transform .18s;}
.lang-btn.active{background:#1a2e1a;color:#fff;box-shadow:0 3px 10px rgba(0,0,0,.22);transform:scale(1.05);}
.page-header{text-align:center;padding:44px 0 32px;}
.ph-inner{display:inline-flex;flex-direction:column;align-items:center;gap:10px;}
.ph-title{font-family:'Fredoka One',cursive;font-size:clamp(2rem,4.5vw,3rem);
  color:#1a2e1a;letter-spacing:.4px;line-height:1;}
.ph-sub{font-size:.92rem;color:#a0a8a0;font-weight:700;margin-top:4px;display:none;}
.lang-badge{display:inline-flex;align-items:center;gap:7px;margin-top:10px;
  padding:6px 18px;border-radius:var(--pill);font-family:'Fredoka One',cursive;
  font-size:.78rem;background:#fff;border:1.5px solid #dde8dd;color:#2a4a2a;
  box-shadow:0 2px 10px rgba(0,0,0,.06);transition:all .3s;}
.lang-badge.tl{background:#fff8f0;border-color:#ffcc80;color:#b84a00;}
.section-header-card{display:flex;align-items:center;gap:16px;background:#fff;border-radius:20px;
  padding:18px 24px;box-shadow:0 4px 18px rgba(0,0,0,.06);margin-bottom:28px;}
.sec-icon-box{width:56px;height:56px;border-radius:16px;display:flex;align-items:center;
  justify-content:center;font-size:1.4rem;flex-shrink:0;color:#fff;box-shadow:0 6px 16px rgba(0,0,0,.12);}
.sec-title-group{flex:1;min-width:0;}
.sec-title{font-family:'Fredoka One',cursive;font-size:1.5rem;color:#1a1a2e;line-height:1.15;margin:0;}
.sec-subtitle{font-size:.875rem;color:#aaa;font-weight:700;margin:3px 0 0;}
.sec-line{flex:1;height:2px;background:linear-gradient(90deg,#e0e0e0 0%,transparent 100%);
  border-radius:99px;margin-left:8px;min-width:40px;}
.letters-grid{display:grid;grid-template-columns:repeat(6,1fr);gap:16px;
  padding-bottom:8px;transition:opacity .28s,transform .28s;}
@media(max-width:1100px){.letters-grid{grid-template-columns:repeat(5,1fr);}}
@media(max-width:860px){.letters-grid{grid-template-columns:repeat(4,1fr);}}
@media(max-width:600px){.letters-grid{grid-template-columns:repeat(3,1fr);}}
@media(max-width:380px){.letters-grid{grid-template-columns:repeat(2,1fr);}}
.letter-card{background:#fff;border-radius:22px;overflow:hidden;cursor:pointer;
  position:relative;border:2px solid transparent;box-shadow:0 4px 18px rgba(0,0,0,.09);
  animation:cardIn .4s ease both;display:flex;flex-direction:column;
  transition:transform .3s cubic-bezier(.34,1.56,.64,1),box-shadow .3s,border-color .25s;}
.letter-card:hover{transform:translateY(-10px) scale(1.04);box-shadow:0 20px 50px rgba(0,0,0,.16);border-color:var(--cc);}
.letter-card:active{transform:scale(.96);}
.card-bar{height:5px;background:var(--cc);flex-shrink:0;}
.card-inner{padding:14px 10px 13px;display:flex;flex-direction:column;align-items:center;gap:0;flex:1;
  background:linear-gradient(180deg,color-mix(in srgb,var(--cc) 7%,white) 0%,#fff 55%);}
.card-letters{display:flex;align-items:baseline;justify-content:center;gap:3px;margin-bottom:5px;}
.card-upper{font-family:'Fredoka One',cursive;font-size:2.5rem;color:var(--cc);line-height:1;
  transition:transform .3s cubic-bezier(.34,1.56,.64,1);display:inline-block;
  filter:drop-shadow(0 2px 0 color-mix(in srgb,var(--cc) 30%,transparent));}
.card-lower{font-family:'Fredoka One',cursive;font-size:1.5rem;color:var(--cc);opacity:.5;line-height:1;
  transition:transform .3s cubic-bezier(.34,1.56,.64,1);display:inline-block;}
.letter-card:hover .card-upper{transform:scale(1.22) rotate(-6deg);}
.letter-card:hover .card-lower{transform:scale(1.15) rotate(5deg);}
.card-sep{width:24px;height:2px;background:var(--cc);opacity:.25;border-radius:2px;margin-bottom:5px;}
.card-word{font-size:.68rem;font-weight:800;color:#b0b8b0;letter-spacing:.7px;text-transform:uppercase;margin-bottom:9px;}
.card-photo{width:100%;aspect-ratio:1/1;overflow:hidden;background:#f0f0f0;border-radius:10px;}
.card-photo img{width:100%;height:100%;object-fit:cover;transition:transform .35s ease;}
.letter-card:hover .card-photo img{transform:scale(1.08);}
.letter-card::after{content:'';position:absolute;inset:0;
  background:linear-gradient(110deg,transparent 35%,rgba(255,255,255,.45) 50%,transparent 65%);
  transform:translateX(-110%);transition:transform .55s ease;pointer-events:none;}
.letter-card:hover::after{transform:translateX(110%);}
.ripple{position:absolute;border-radius:50%;transform:scale(0);animation:rippleAnim .6s linear;pointer-events:none;}
@keyframes rippleAnim{to{transform:scale(4);opacity:0;}}
@keyframes cardIn{from{opacity:0;transform:translateY(22px) scale(.92);}to{opacity:1;transform:none;}}
<?php foreach(range(1,26) as $i): ?>
.letter-card:nth-child(<?=$i?>){animation-delay:<?=round($i*.036,3)?>s;}
<?php endforeach;?>
.write-section{padding:40px 0 60px;}
.picker-wrap{margin-bottom:36px;background:#fff;border-radius:24px;
  box-shadow:0 4px 24px rgba(0,0,0,.08);border:2px solid #eeeae2;padding:20px 20px 18px;}
.picker-label{font-family:'Fredoka One',cursive;font-size:.75rem;letter-spacing:1.2px;
  text-transform:uppercase;color:#b0a8a0;margin-bottom:12px;display:flex;align-items:center;gap:8px;}
.picker-grid{display:flex;flex-wrap:wrap;gap:10px;justify-content:center;}
.pick-key{width:52px;height:52px;border-radius:50%;cursor:pointer;flex-shrink:0;
  background:#f7f5f0;border:2px solid #e8e4dc;
  display:flex;flex-direction:column;align-items:center;justify-content:center;
  gap:0;transition:all .2s cubic-bezier(.34,1.56,.64,1);position:relative;}
.pick-key:hover{background:var(--pc);border-color:var(--pc);transform:scale(1.18) translateY(-3px);box-shadow:0 8px 22px rgba(0,0,0,.15);}
.pick-key:hover .pk-u,.pick-key:hover .pk-l{color:#fff;}
.pick-key.sel{background:var(--pc);border-color:var(--pc);transform:scale(1.22) translateY(-4px);box-shadow:0 10px 28px rgba(0,0,0,.18);}
.pick-key.sel .pk-u,.pick-key.sel .pk-l{color:#fff;}
.pick-key.sel::after{content:'';position:absolute;inset:-4px;border-radius:50%;border:2px solid var(--pc);opacity:.4;animation:keyPulse 1.5s ease-in-out infinite;}
@keyframes keyPulse{0%,100%{transform:scale(1);opacity:.4;}50%{transform:scale(1.15);opacity:.1;}}
.pk-u{font-family:'Fredoka One',cursive;font-size:1.35rem;color:var(--pc);line-height:1;}
.pk-l{font-family:'Fredoka One',cursive;font-size:.75rem;color:var(--pc);opacity:.5;line-height:1;}
.write-board{max-width:780px;margin:0 auto;background:#fff;border-radius:28px;
  box-shadow:0 16px 56px rgba(0,0,0,.1);border:2px solid #eaeeea;overflow:hidden;display:none;}
.write-board.visible{display:block;animation:boardIn .42s cubic-bezier(.34,1.56,.64,1);}
@keyframes boardIn{from{opacity:0;transform:translateY(20px) scale(.96);}to{opacity:1;transform:none;}}
.wb-head{padding:22px 28px 18px;border-bottom:1.5px solid #f0f4f0;display:flex;align-items:center;gap:18px;}
.wb-letters-big{display:flex;align-items:baseline;gap:6px;flex-shrink:0;}
.wb-big-u{font-family:'Fredoka One',cursive;font-size:3.8rem;line-height:1;color:var(--wbc,#333);}
.wb-big-l{font-family:'Fredoka One',cursive;font-size:2.4rem;line-height:1;color:var(--wbc,#333);opacity:.4;}
.wb-meta{flex:1;}
.wb-word{font-family:'Fredoka One',cursive;font-size:1.35rem;color:#2a2a2a;margin-bottom:3px;}
.wb-sub{font-size:.75rem;font-weight:800;color:#b0bfae;display:flex;align-items:center;gap:5px;text-transform:uppercase;letter-spacing:.4px;}
.wb-panels{display:flex;background:#e8e4d8;border-bottom:1.5px solid #dddad0;gap:0;}
.wb-panel{flex:1;padding:28px 20px 24px;display:flex;flex-direction:column;align-items:center;gap:10px;}
.wb-panel+.wb-panel{border-left:1.5px solid #dddad0;}
.wb-panel-label{font-family:'Fredoka One',cursive;font-size:.8rem;color:#8a8070;text-transform:uppercase;letter-spacing:.7px;
  display:flex;align-items:center;gap:6px;background:#fff;padding:5px 14px;border-radius:20px;
  box-shadow:0 2px 8px rgba(0,0,0,.08);border:1px solid #e0ddd5;}
.paper-display{width:200px;height:260px;border-radius:4px 12px 12px 4px;background:#fffef5;
  position:relative;overflow:hidden;
  box-shadow:2px 0 0 #e0ddc8,4px 0 0 #d8d5c0,0 4px 16px rgba(0,0,0,.12),inset 0 0 0 1px rgba(0,0,0,.05);}
.paper-display::before{content:'';position:absolute;left:28px;top:0;bottom:0;width:1.5px;background:rgba(210,60,60,.4);z-index:2;pointer-events:none;}
.paper-display::after{content:'';position:absolute;left:8px;top:0;bottom:0;width:18px;
  background:radial-gradient(circle at 50% 12%,#c8c0a8 4px,transparent 4px),
    radial-gradient(circle at 50% 27%,#c8c0a8 4px,transparent 4px),
    radial-gradient(circle at 50% 42%,#c8c0a8 4px,transparent 4px),
    radial-gradient(circle at 50% 57%,#c8c0a8 4px,transparent 4px),
    radial-gradient(circle at 50% 72%,#c8c0a8 4px,transparent 4px),
    radial-gradient(circle at 50% 87%,#c8c0a8 4px,transparent 4px);
  z-index:3;pointer-events:none;}
.paper-display svg{position:absolute;inset:0;width:100%;height:100%;}
.wb-foot{padding:16px 28px 18px;display:flex;align-items:center;gap:12px;flex-wrap:wrap;}
.wb-hint{flex:1;font-size:.8rem;font-weight:800;color:#c0c8c0;display:flex;align-items:center;gap:7px;min-width:140px;}
.wc-btn{font-family:'Fredoka One',cursive;font-size:.9rem;padding:10px 22px;border-radius:var(--pill);
  border:none;cursor:pointer;display:inline-flex;align-items:center;gap:8px;
  transition:transform .2s,box-shadow .2s,opacity .2s;}
.wc-btn:hover{transform:scale(1.06);}
.wc-btn:disabled{opacity:.55;cursor:not-allowed;transform:none;}
.wc-btn.primary{color:#fff;box-shadow:0 6px 18px rgba(0,0,0,.18);}
.wc-btn.secondary{background:#f0f2f0;color:#5a6a58;box-shadow:0 2px 8px rgba(0,0,0,.07);}
.vc-section{padding:40px 0 60px;}
.vc-legend{display:flex;align-items:center;justify-content:center;gap:24px;
  margin:0 auto 36px;padding:16px 28px;background:#fff;border-radius:20px;max-width:480px;
  box-shadow:0 4px 20px rgba(0,0,0,.08);border:2px solid #eee;}
.vc-leg-item{display:flex;align-items:center;gap:10px;}
.vc-leg-dot{width:18px;height:18px;border-radius:50%;flex-shrink:0;}
.vc-leg-dot.v{background:var(--vowel);}
.vc-leg-dot.c{background:var(--cons);}
.vc-leg-label{font-family:'Fredoka One',cursive;font-size:.95rem;color:#3a3a3a;}
.vc-leg-count{font-size:.7rem;font-weight:800;color:#aaa;}
.vc-leg-sep{width:1.5px;height:28px;background:#eee;}
.vc-alpha-grid{display:flex;flex-wrap:wrap;gap:10px;justify-content:flex-start;margin-bottom:36px;}
.vc-card{width:76px;min-height:84px;border-radius:18px;overflow:hidden;cursor:pointer;
  position:relative;border:2.5px solid transparent;box-shadow:0 3px 14px rgba(0,0,0,.08);
  display:flex;flex-direction:column;animation:cardIn .4s ease both;
  transition:transform .3s cubic-bezier(.34,1.56,.64,1),box-shadow .3s;background:#fff;}
.vc-card:hover{transform:translateY(-8px) scale(1.07);}
.vc-card.vow{border-color:rgba(232,51,109,.2);}
.vc-card.vow .vc-cbar{background:var(--vowel);}
.vc-card.vow .vc-cinner{background:linear-gradient(180deg,var(--vowel-light) 0%,#fff 60%);}
.vc-card.vow .vc-cu{color:var(--vowel);}
.vc-card.vow .vc-cl{color:var(--vowel);}
.vc-card.vow .vc-cbadge{background:var(--vowel);color:#fff;}
.vc-card.vow:hover{border-color:var(--vowel);box-shadow:0 16px 36px rgba(232,51,109,.22);}
.vc-card.con{border-color:rgba(26,109,232,.2);}
.vc-card.con .vc-cbar{background:var(--cons);}
.vc-card.con .vc-cinner{background:linear-gradient(180deg,var(--cons-light) 0%,#fff 60%);}
.vc-card.con .vc-cu{color:var(--cons);}
.vc-card.con .vc-cl{color:var(--cons);}
.vc-card.con .vc-cbadge{background:var(--cons);color:#fff;}
.vc-card.con:hover{border-color:var(--cons);box-shadow:0 16px 36px rgba(26,109,232,.22);}
.vc-card::after{content:'';position:absolute;inset:0;
  background:linear-gradient(110deg,transparent 35%,rgba(255,255,255,.5) 50%,transparent 65%);
  transform:translateX(-110%);transition:transform .55s ease;pointer-events:none;}
.vc-card:hover::after{transform:translateX(110%);}
.vc-cbar{height:4px;flex-shrink:0;}
.vc-cinner{padding:8px 6px 8px;display:flex;flex-direction:column;align-items:center;flex:1;}
.vc-cu{font-family:'Fredoka One',cursive;font-size:1.9rem;line-height:1;transition:transform .3s cubic-bezier(.34,1.56,.64,1);display:inline-block;}
.vc-cl{font-family:'Fredoka One',cursive;font-size:1.1rem;opacity:.4;line-height:1;transition:transform .3s cubic-bezier(.34,1.56,.64,1);display:inline-block;}
.vc-card:hover .vc-cu{transform:scale(1.2) rotate(-5deg);}
.vc-card:hover .vc-cl{transform:scale(1.12) rotate(4deg);}
.vc-cbadge{font-family:'Fredoka One',cursive;font-size:.52rem;padding:2px 7px;border-radius:20px;letter-spacing:.3px;text-transform:uppercase;margin-top:3px;}
:root{--song:#7c3aed;--song2:#a855f7;--song-light:#f5f0ff;}
.song-section{padding:32px 0 60px;}
.song-video-card{background:#fff;border-radius:24px;overflow:hidden;
  box-shadow:0 12px 44px rgba(124,58,237,.14);border:2px solid rgba(124,58,237,.1);}
.song-vtabs{display:flex;gap:8px;padding:16px 18px 0;flex-wrap:wrap;}
.svtab{font-family:'Fredoka One',cursive;font-size:.8rem;padding:7px 16px;
  border-radius:var(--pill);border:2px solid #e8e0f0;background:#fff;color:#9880c0;cursor:pointer;
  transition:all .2s;display:flex;align-items:center;gap:6px;}
.svtab:hover{border-color:var(--song2);color:var(--song);}
.svtab.active{background:linear-gradient(135deg,var(--song),var(--song2));color:#fff;border-color:transparent;box-shadow:0 4px 14px rgba(124,58,237,.28);}
.song-yt-wrap{padding:16px 18px 18px;}
.song-yt-box{width:100%;aspect-ratio:16/9;border-radius:14px;overflow:hidden;background:#0a0a0a;position:relative;box-shadow:0 6px 24px rgba(0,0,0,.18);}
.song-yt-box iframe{width:100%;height:100%;border:none;display:none;}
.song-yt-placeholder{position:absolute;inset:0;background:linear-gradient(135deg,#2d0b6e,#5b21b6);
  display:flex;flex-direction:column;align-items:center;justify-content:center;gap:14px;cursor:pointer;}
.song-yt-placeholder.gone{display:none;}
.song-play-btn{width:68px;height:68px;border-radius:50%;background:#fff;
  display:flex;align-items:center;justify-content:center;font-size:1.6rem;color:var(--song);
  box-shadow:0 8px 24px rgba(124,58,237,.4);transition:transform .2s cubic-bezier(.34,1.56,.64,1);}
.song-yt-placeholder:hover .song-play-btn{transform:scale(1.15);}
.song-ph-title{font-family:'Fredoka One',cursive;font-size:1.15rem;color:#fff;text-align:center;padding:0 20px;}
.song-ph-sub{font-size:.75rem;font-weight:800;color:rgba(255,255,255,.6);}
.modal-overlay{display:none;position:fixed;inset:0;background:rgba(10,18,10,.56);
  backdrop-filter:blur(12px);z-index:500;align-items:center;justify-content:center;padding:20px;}
.modal-overlay.active{display:flex;}
.modal-box{background:#fff;border-radius:30px;max-width:390px;width:100%;
  position:relative;box-shadow:0 40px 100px rgba(0,0,0,.28);
  animation:modalIn .35s cubic-bezier(.34,1.56,.64,1) both;overflow:hidden;}
@keyframes modalIn{from{opacity:0;transform:scale(.6) translateY(40px);}to{opacity:1;transform:none;}}
.modal-stripe{height:8px;}
.modal-body{padding:30px 26px 26px;text-align:center;position:relative;}
.modal-wm{position:absolute;top:-14px;right:-8px;font-family:'Fredoka One',cursive;font-size:130px;
  opacity:.055;line-height:1;pointer-events:none;user-select:none;color:var(--mc);}
.modal-close{position:absolute;top:14px;right:14px;width:32px;height:32px;border-radius:50%;
  border:none;background:#f2f2f2;cursor:pointer;display:flex;align-items:center;justify-content:center;
  color:#888;font-size:.95rem;transition:background .2s,transform .2s;z-index:2;}
.modal-close:hover{background:#ffe0e0;color:#e53935;transform:rotate(90deg);}
.modal-letters-big{display:flex;align-items:baseline;justify-content:center;gap:5px;
  margin-bottom:6px;animation:floatBounce 3s ease-in-out infinite;}
@keyframes floatBounce{0%,100%{transform:translateY(0) rotate(-3deg);}50%{transform:translateY(-12px) rotate(3deg);}}
.m-upper{font-family:'Fredoka One',cursive;font-size:4.8rem;color:var(--mc);line-height:1;}
.m-lower{font-family:'Fredoka One',cursive;font-size:3rem;color:var(--mc);opacity:.45;line-height:1;}
.m-word{font-family:'Fredoka One',cursive;font-size:1.75rem;color:#2a2a2a;margin-bottom:14px;}
.m-photo{width:100%;height:175px;border-radius:16px;overflow:hidden;border:3px solid var(--mc);margin-bottom:18px;box-shadow:0 6px 18px rgba(0,0,0,.1);}
.m-photo img{width:100%;height:100%;object-fit:cover;}
.m-sound-btn{background:var(--mc);color:#fff;border:none;border-radius:var(--pill);padding:11px 32px;
  font-family:'Fredoka One',cursive;font-size:1.05rem;cursor:pointer;box-shadow:0 6px 18px rgba(0,0,0,.15);
  display:inline-flex;align-items:center;gap:9px;transition:transform .2s,box-shadow .2s;}
.m-sound-btn:hover{transform:scale(1.06);box-shadow:0 10px 26px rgba(0,0,0,.2);}
.m-sound-btn:disabled{opacity:.65;cursor:not-allowed;transform:none;}
.m-vc-badge{display:inline-flex;align-items:center;gap:7px;padding:4px 14px;border-radius:20px;
  margin-bottom:10px;font-family:'Fredoka One',cursive;font-size:.75rem;}
.m-vc-badge.vow{background:var(--vowel-light);color:var(--vowel);}
.m-vc-badge.con{background:var(--cons-light);color:var(--cons);}
.m-vc-words{display:flex;flex-wrap:wrap;justify-content:center;gap:7px;margin-bottom:14px;}
.m-vc-chip{font-size:.8rem;font-weight:800;padding:5px 13px;border-radius:28px;cursor:pointer;transition:all .2s;}
.m-vc-chip.vow{background:var(--vowel-light);color:var(--vowel-dark);}
.m-vc-chip.con{background:var(--cons-light);color:var(--cons-dark);}
.m-vc-chip:hover{transform:scale(1.1);}
.cfbit{position:fixed;pointer-events:none;z-index:1000;animation:cfFall linear forwards;}
@keyframes cfFall{0%{transform:translateY(-16px) rotate(0deg);opacity:1;}100%{transform:translateY(105vh) rotate(700deg);opacity:0;}}

/* ===== ACTIVITY SECTIONS ===== */
.activity-section{padding:40px 0 20px;}
.activity-divider{text-align:center;padding:20px 0 40px;position:relative;}
.activity-divider::before{content:'';position:absolute;top:50%;left:0;right:0;height:2px;
  background:linear-gradient(90deg,transparent,#e0d8cc 20%,#e0d8cc 80%,transparent);pointer-events:none;}
.activity-divider-badge{display:inline-flex;align-items:center;gap:10px;
  background:var(--cream);padding:6px 20px;border-radius:var(--pill);position:relative;z-index:1;
  font-family:'Fredoka One',cursive;font-size:.88rem;color:#aaa;border:2px solid #e8e0d8;}
.activity-divider-badge i{color:#FFD93D;}
.act-card{background:#fff;border-radius:24px;padding:28px 24px;
  box-shadow:0 6px 28px rgba(0,0,0,.08);margin-bottom:28px;border:2px solid #f0ece4;
  transition:box-shadow .2s;}

/* ACTIVITY 1: Letter-Picture Match */
.match-question{text-align:center;margin-bottom:22px;}
.match-letter-big{font-family:'Fredoka One',cursive;font-size:5rem;line-height:1;display:inline-block;
  animation:floatBounce 3s ease-in-out infinite;}
.match-instruction{font-family:'Fredoka One',cursive;font-size:.9rem;color:#b0a8a0;margin-top:6px;}
.match-pics-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;}
@media(max-width:600px){.match-pics-grid{grid-template-columns:repeat(2,1fr);}}
.match-pic-card{background:#fff;border-radius:18px;overflow:hidden;cursor:pointer;
  border:3px solid #f0ece4;transition:transform .25s cubic-bezier(.34,1.56,.64,1),border-color .2s,box-shadow .2s;
  box-shadow:0 4px 16px rgba(0,0,0,.08);display:flex;flex-direction:column;}
.match-pic-card:hover{transform:translateY(-6px) scale(1.03);box-shadow:0 12px 32px rgba(0,0,0,.14);}
.match-pic-card.correct{border-color:#6BCB77;background:#e8f8ea;animation:popBounce .4s ease;}
.match-pic-card.wrong{border-color:#FF6B6B;background:#fff0f0;animation:shake .35s ease;}
.match-pic-card.disabled{pointer-events:none;opacity:.55;}
.match-pic-img{width:100%;aspect-ratio:1/1;overflow:hidden;background:#f7f5f0;border-bottom:2px solid #f0ece4;}
.match-pic-img img{width:100%;height:100%;object-fit:cover;display:block;transition:transform .35s ease;}
.match-pic-card:hover .match-pic-img img{transform:scale(1.07);}
.match-pic-label{font-family:'Fredoka One',cursive;font-size:.9rem;color:#555;padding:10px 8px;text-align:center;}
.match-next-btn{display:inline-flex;margin:18px auto 0;font-family:'Fredoka One',cursive;font-size:1rem;
  padding:11px 32px;border-radius:var(--pill);border:none;color:#fff;cursor:pointer;
  box-shadow:0 6px 18px rgba(0,0,0,.14);transition:transform .2s,box-shadow .2s,opacity .2s;
  opacity:0;pointer-events:none;align-items:center;gap:8px;}
.match-next-btn.show{opacity:1;pointer-events:auto;}
.match-next-btn:hover{transform:scale(1.06);}

/* ACTIVITY 2: Fill in the Missing Letter */
.missing-row{display:flex;align-items:center;justify-content:center;gap:10px;flex-wrap:wrap;margin-bottom:24px;}
.ml-box{width:64px;height:72px;border-radius:16px;display:flex;align-items:center;justify-content:center;
  font-family:'Fredoka One',cursive;font-size:2rem;border:3px solid transparent;}
.ml-box.given{background:#f0ece4;border-color:#e0d8cc;color:#333;}
.ml-box.blank{background:#fdf8f0;border:3px dashed #ccc;color:#ccc;cursor:default;}
.ml-choices{display:flex;justify-content:center;gap:12px;flex-wrap:wrap;margin-bottom:8px;}
.ml-choice{width:60px;height:60px;border-radius:50%;font-family:'Fredoka One',cursive;font-size:1.5rem;
  border:3px solid #e0d8cc;background:#fff;cursor:pointer;
  transition:transform .25s cubic-bezier(.34,1.56,.64,1),border-color .2s,background .2s;
  display:flex;align-items:center;justify-content:center;}
.ml-choice:hover{transform:scale(1.18) translateY(-3px);border-color:var(--act-color,#A29BFE);}
.ml-choice.correct{background:#6BCB77;border-color:#3d8b37;color:#fff;animation:popBounce .4s ease;}
.ml-choice.wrong{background:#FF6B6B;border-color:#c0392b;color:#fff;animation:shake .35s ease;}

/* ACTIVITY 3 — KID WRITING */
.kwrite-section{padding:40px 0 60px;}
.kwrite-canvas-wrap{display:flex;flex-direction:column;align-items:center;gap:16px;}
.kwrite-canvas-row{display:flex;gap:24px;justify-content:center;flex-wrap:wrap;}
.kwrite-canvas-box{display:flex;flex-direction:column;align-items:center;gap:8px;}
.kwrite-canvas-label{font-family:'Fredoka One',cursive;font-size:.8rem;color:#8a8070;
  text-transform:uppercase;letter-spacing:.7px;background:#fff;padding:4px 14px;border-radius:20px;
  box-shadow:0 2px 8px rgba(0,0,0,.08);border:1px solid #e0ddd5;}
.kwrite-canvas{border-radius:14px;background:#fffef8;cursor:crosshair;
  box-shadow:inset 0 2px 12px rgba(0,0,0,.06),0 4px 18px rgba(0,0,0,.08);
  touch-action:none;display:block;border:2px solid #e8e4d8;}
.kwrite-canvas.error-flash{animation:errorFlash .5s ease;}
@keyframes errorFlash{0%,100%{border-color:#e8e4d8;box-shadow:inset 0 2px 12px rgba(0,0,0,.06),0 4px 18px rgba(0,0,0,.08);}
  30%,70%{border-color:#FF6B6B;box-shadow:inset 0 2px 12px rgba(255,107,107,.2),0 4px 18px rgba(255,107,107,.18);}}
.kwrite-btns{display:flex;gap:10px;justify-content:center;flex-wrap:wrap;margin-top:6px;}
.kwrite-btn{font-family:'Fredoka One',cursive;font-size:.88rem;padding:10px 24px;
  border-radius:var(--pill);border:none;cursor:pointer;display:inline-flex;align-items:center;gap:8px;
  transition:transform .2s,box-shadow .2s;}
.kwrite-btn:hover{transform:scale(1.06);}
.kwrite-btn.check{color:#fff;box-shadow:0 6px 16px rgba(0,0,0,.14);}
.kwrite-btn.clear{background:#f0f2f0;color:#5a6a58;box-shadow:0 2px 8px rgba(0,0,0,.07);}

/* Letter display banner in kwrite */
.kwrite-letter-banner{
  display:flex;align-items:center;justify-content:center;gap:16px;
  background:#fff;border-radius:20px;padding:16px 28px;
  box-shadow:0 4px 18px rgba(0,0,0,.06);border:2px solid #f0ece4;
  margin-bottom:24px;
}
.kwrite-banner-letter{
  font-family:'Fredoka One',cursive;font-size:3.5rem;line-height:1;
  animation:floatBounce 3s ease-in-out infinite;
}
.kwrite-banner-word{
  font-family:'Fredoka One',cursive;font-size:1.1rem;color:#aaa;
  display:flex;flex-direction:column;gap:2px;
}
.kwrite-banner-word span:first-child{color:#555;font-size:1.3rem;}

/* ---- Big emoji result pop ---- */
.kwrite-emoji-pop{
  font-size:5rem;text-align:center;
  opacity:0;transform:scale(.3) translateY(20px);
  transition:opacity .25s,transform .35s cubic-bezier(.34,1.56,.64,1);
  pointer-events:none;line-height:1;margin-top:8px;
}
.kwrite-emoji-pop.show{opacity:1;transform:scale(1) translateY(0);}

@keyframes popBounce{0%{transform:scale(1);}40%{transform:scale(1.32);}70%{transform:scale(.93);}100%{transform:scale(1.18);}}
@keyframes shake{0%,100%{transform:translateX(0);}20%{transform:translateX(-7px);}40%{transform:translateX(7px);}60%{transform:translateX(-5px);}80%{transform:translateX(5px);}}
</style>
</head>
<body>

<!-- Screen flash overlay for activities -->
<div id="screenFlash"></div>

<div class="page-wrap">

<nav class="lesson-nav">
  <a href="lessons.php" class="lnav-back"><i class="fas fa-arrow-left"></i> Back</a>
  <div class="lnav-spacer"></div>
  <div class="lang-toggle">
    <button class="lang-btn active" id="btnEN" onclick="setLang('en')"><i class="fas fa-flag-usa"></i> EN</button>
    <button class="lang-btn" id="btnTL" onclick="setLang('tl')"><i class="fas fa-flag"></i> TL</button>
  </div>
</nav>

<div class="container">
  <div class="page-header">
    <div class="ph-inner">
      <div class="ph-title" id="pageTitle">Learn the Letters!</div>
      <div class="ph-sub" id="pageSub"></div>
      <div class="lang-badge" id="langBadge">
        <i class="fas fa-book-open"></i>
        <span id="badgeText">English Alphabet &mdash; 26 Letters</span>
      </div>
    </div>
  </div>
</div>

<!-- LETTERS GRID -->
<div class="container" style="padding-bottom:16px">
  <div class="section-header-card">
    <div class="sec-icon-box" style="background:linear-gradient(135deg,#1a2e1a,#3d8b37);">
      <i class="fas fa-th-large"></i>
    </div>
    <div class="sec-title-group">
      <div class="sec-title" id="secLettersTitle">Letters</div>
      <div class="sec-subtitle" id="secLettersSub">Tap a letter card to explore it!</div>
    </div>
    <div class="sec-line"></div>
  </div>
  <div class="letters-grid" id="lettersGrid"></div>
</div>

<!-- WRITE SECTION -->
<div class="write-section">
  <div class="container">
    <div class="section-header-card">
      <div class="sec-icon-box" style="background:linear-gradient(135deg,#7b3fa0,#c060e8);">
        <i class="fas fa-magic"></i>
      </div>
      <div class="sec-title-group">
        <div class="sec-title" id="writeTitle">Stroke by Stroke!</div>
        <div class="sec-subtitle" id="writeSub">Choose a letter and watch how each stroke is made on the writing lines</div>
      </div>
      <div class="sec-line"></div>
    </div>
    <div class="picker-wrap">
      <div class="picker-label"><i class="fas fa-hand-pointer"></i> <span id="pickerLabel">Pick a letter</span></div>
      <div class="picker-grid" id="letterPicker"></div>
    </div>
    <div class="write-board" id="writeBoard">
      <div class="wb-head">
        <div class="wb-letters-big">
          <span class="wb-big-u" id="wbU">A</span>
          <span class="wb-big-l" id="wbL">a</span>
        </div>
        <div class="wb-meta">
          <div class="wb-word" id="wbWord">Apple</div>
          <div class="wb-sub"><i class="fas fa-eye"></i> <span id="wbSubText">Watch each stroke carefully</span></div>
        </div>
      </div>
      <div class="wb-panels">
        <div class="wb-panel">
          <div class="wb-panel-label"><i class="fas fa-arrow-up"></i> <span id="capLabel">Capital</span></div>
          <div class="paper-display" id="paperU">
            <svg id="svgU" viewBox="0 0 200 260" xmlns="http://www.w3.org/2000/svg"></svg>
          </div>
        </div>
        <div class="wb-panel">
          <div class="wb-panel-label"><i class="fas fa-arrow-down"></i> <span id="smallLabel">Small</span></div>
          <div class="paper-display" id="paperL">
            <svg id="svgL" viewBox="0 0 200 260" xmlns="http://www.w3.org/2000/svg"></svg>
          </div>
        </div>
      </div>
      <div class="wb-foot">
        <div class="wb-hint"><i class="fas fa-lightbulb"></i> <span id="wbHint">Press Play to watch the stroke order</span></div>
        <button class="wc-btn primary" id="btnPlay" onclick="doTrace()">
          <i class="fas fa-play" id="playIco"></i>
          <span id="playLbl">Watch me write!</span>
        </button>
        <button class="wc-btn secondary" onclick="resetTrace()">
          <i class="fas fa-undo"></i> <span id="resetLbl">Reset</span>
        </button>
      </div>
    </div>
  </div>
</div>

<!-- VOWELS & CONSONANTS -->
<div class="vc-section">
  <div class="container">
    <div class="section-header-card">
      <div class="sec-icon-box" style="background:linear-gradient(135deg,#a3184a,#e8336d);">
        <i class="fas fa-star"></i>
      </div>
      <div class="sec-title-group">
        <div class="sec-title" id="vcTitle">Vowels &amp; Consonants!</div>
        <div class="sec-subtitle" id="vcSub">Learn the difference — tap any letter below!</div>
      </div>
      <div class="sec-line"></div>
    </div>
    <div class="vc-legend">
      <div class="vc-leg-item">
        <div class="vc-leg-dot v"></div>
        <div class="vc-leg-label"><span id="vcLegV">Vowel</span> <span class="vc-leg-count" id="vcVCount">(5)</span></div>
      </div>
      <div class="vc-leg-sep"></div>
      <div class="vc-leg-item">
        <div class="vc-leg-dot c"></div>
        <div class="vc-leg-label"><span id="vcLegC">Consonant</span> <span class="vc-leg-count" id="vcCCount">(21)</span></div>
      </div>
    </div>
    <div class="section-header-card" style="margin-bottom:20px;">
      <div class="sec-icon-box" style="background:linear-gradient(135deg,#1a6de8,#74c0fc);font-size:1.1rem;">
        <i class="fas fa-th"></i>
      </div>
      <div class="sec-title-group">
        <div class="sec-title" style="font-size:1.2rem;" id="vcGridLabel">All Letters — Color Coded</div>
        <div class="sec-subtitle" id="vcGridSub">Pink = Vowel &nbsp;·&nbsp; Blue = Consonant</div>
      </div>
      <div class="sec-line"></div>
    </div>
    <div class="vc-alpha-grid" id="vcAlphaGrid"></div>
  </div>
</div>

<!-- ACTIVITY DIVIDER -->
<div class="container">
  <div class="activity-divider">
    <div class="activity-divider-badge">
      <i class="fas fa-gamepad"></i>
      <span id="actDividerText">Now let's play!</span>
    </div>
  </div>
</div>

<!-- ACTIVITY 1 — Letter-Picture Match -->
<div class="activity-section">
  <div class="container">
    <div class="section-header-card">
      <div class="sec-icon-box" style="background:linear-gradient(135deg,#FF6B6B,#FF8E53);">
        <i class="fas fa-images"></i>
      </div>
      <div class="sec-title-group">
        <div class="sec-title" id="matchTitle">Picture Match!</div>
        <div class="sec-subtitle" id="matchSub">Find the picture that starts with the letter!</div>
      </div>
      <div class="sec-line"></div>
    </div>
    <div class="act-card">
      <div class="match-question">
        <div class="match-letter-big" id="matchLetterBig" style="color:#FF6B6B">A</div>
        <div class="match-instruction" id="matchInstruction">Tap the picture that starts with this letter!</div>
      </div>
      <div class="match-pics-grid" id="matchPicsGrid"></div>
      <div style="text-align:center;">
        <button class="match-next-btn" id="matchNextBtn" onclick="nextMatchQ()" style="background:#FF6B6B;">
          <i class="fas fa-arrow-right"></i> <span id="matchNextLbl">Next!</span>
        </button>
      </div>
    </div>
  </div>
</div>

<!-- ACTIVITY 2 — Missing Letter -->
<div class="activity-section">
  <div class="container">
    <div class="section-header-card">
      <div class="sec-icon-box" style="background:linear-gradient(135deg,#A29BFE,#845EC2);">
        <i class="fas fa-question-circle"></i>
      </div>
      <div class="sec-title-group">
        <div class="sec-title" id="missingTitle">What's the Missing Letter?</div>
        <div class="sec-subtitle" id="missingSub">Fill in the blank — tap the right letter!</div>
      </div>
      <div class="sec-line"></div>
    </div>
    <div class="act-card">
      <div class="missing-row" id="missingRow"></div>
      <div class="ml-choices" id="mlChoices"></div>
      <div style="text-align:center;">
        <button class="match-next-btn" id="missingNextBtn" onclick="nextMissingQ()" style="background:#A29BFE;">
          <i class="fas fa-arrow-right"></i> <span id="missingNextLbl">Next!</span>
        </button>
      </div>
    </div>
  </div>
</div>

<!-- ACTIVITY 3 — Kid Writing / Tracing -->
<div class="kwrite-section">
  <div class="container">
    <div class="section-header-card">
      <div class="sec-icon-box" style="background:linear-gradient(135deg,#00ACC1,#007B8A);">
        <i class="fas fa-pencil-alt"></i>
      </div>
      <div class="sec-title-group">
        <div class="sec-title" id="kwriteTitle">Your Turn to Write!</div>
        <div class="sec-subtitle" id="kwriteSub">A random letter will appear — write both capital and small!</div>
      </div>
      <div class="sec-line"></div>
    </div>

    <div class="act-card">
      <!-- Letter banner -->
      <div class="kwrite-letter-banner" id="kwriteBanner">
        <div class="kwrite-banner-letter" id="kwriteBannerLetter" style="color:#00ACC1">A / a</div>
        <div class="kwrite-banner-word">
          <span id="kwriteBannerWord">Apple</span>
          <span id="kwritePromptLabel" style="font-size:.75rem;">Write both letters below!</span>
        </div>
      </div>

      <div class="kwrite-canvas-wrap" id="kwriteCanvasWrap">
        <div class="kwrite-canvas-row">
          <div class="kwrite-canvas-box">
            <div class="kwrite-canvas-label" id="kwriteCapLabel">Capital</div>
            <div style="position:relative;display:inline-block;">
              <canvas class="kwrite-canvas" id="canvasU" width="200" height="220"></canvas>
              <div id="guideU" style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;font-family:'Fredoka One',cursive;font-size:120px;opacity:.07;color:#888;pointer-events:none;user-select:none;"></div>
            </div>
          </div>
          <div class="kwrite-canvas-box">
            <div class="kwrite-canvas-label" id="kwriteSmallLabel">Small</div>
            <div style="position:relative;display:inline-block;">
              <canvas class="kwrite-canvas" id="canvasL" width="200" height="220"></canvas>
              <div id="guideL" style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;font-family:'Fredoka One',cursive;font-size:110px;opacity:.07;color:#888;pointer-events:none;user-select:none;"></div>
            </div>
          </div>
        </div>

        <div style="text-align:center;margin-top:4px;font-size:.75rem;font-weight:800;color:#bbb;font-family:'Fredoka One',cursive;" id="kwriteRef">
          Use the faint letter as a guide — trace over it!
        </div>

        <div class="kwrite-btns">
          <button class="kwrite-btn check" id="kwriteCheckBtn" onclick="checkWriting()" style="background:#00ACC1;">
            <i class="fas fa-check"></i> <span id="kwriteCheckLbl">Check!</span>
          </button>
          <button class="kwrite-btn clear" onclick="clearCanvases()">
            <i class="fas fa-eraser"></i> <span id="kwriteClearLbl">Clear</span>
          </button>
        </div>

        <!-- Big emoji result -->
        <div class="kwrite-emoji-pop" id="kwriteEmojiPop"></div>

        <!-- Action buttons shown ONLY after check -->
        <div class="kwrite-action-btns" id="kwriteActionBtns" style="display:none;justify-content:center;gap:10px;margin-top:14px;flex-wrap:wrap;">
          <button class="match-next-btn show" id="kwriteTryAgainBtn"
            onclick="clearCanvases()"
            style="background:#FF8E53;position:static;">
            <i class="fas fa-redo"></i> <span id="kwriteTryAgainLbl">Try Again</span>
          </button>
          <button class="match-next-btn" id="kwriteNextBtn"
            onclick="loadRandomKWriteLetter()"
            style="background:#00ACC1;position:static;">
            <i class="fas fa-arrow-right"></i> <span id="kwriteNextLbl">Next Letter!</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- ABC SONG -->
<div class="song-section">
  <div class="container">
    <div class="section-header-card">
      <div class="sec-icon-box" style="background:linear-gradient(135deg,#5b21b6,#7c3aed);">
        <i class="fas fa-headphones"></i>
      </div>
      <div class="sec-title-group">
        <div class="sec-title" id="songTitle">Sing the ABC Song!</div>
        <div class="sec-subtitle" id="songSub">Watch and sing along — tap a video to play it!</div>
      </div>
      <div class="sec-line"></div>
    </div>
    <div class="song-video-card">
      <div class="song-vtabs" id="songVTabs"></div>
      <div class="song-yt-wrap">
        <div class="song-yt-box" id="songYtBox">
          <div class="song-yt-placeholder" id="songYtPlaceholder" onclick="loadSongVideo()">
            <div class="song-play-btn"><i class="fas fa-play"></i></div>
            <div class="song-ph-title" id="songPhTitle">ABC Song for Kids</div>
            <div class="song-ph-sub" id="songPhSub">Tap to play the video</div>
          </div>
          <iframe id="songYtFrame" src="" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- LETTER MODAL -->
<div class="modal-overlay" id="mOverlay" onclick="closeMBg(event)">
  <div class="modal-box" id="mBox">
    <div class="modal-stripe" id="mStripe"></div>
    <div class="modal-body">
      <div class="modal-wm" id="mWm">A</div>
      <button class="modal-close" onclick="closeM()"><i class="fas fa-times"></i></button>
      <div class="m-vc-badge" id="mVCBadge" style="display:none">
        <i class="fas fa-star"></i> <span id="mVCType">Vowel</span>
      </div>
      <div class="modal-letters-big">
        <span class="m-upper" id="mU">A</span>
        <span class="m-lower" id="mL">a</span>
      </div>
      <div class="m-word" id="mWord">Apple</div>
      <div class="m-photo" id="mPhoto">
        <img id="mImg" src="" alt="" onerror="document.getElementById('mPhoto').style.display='none'">
      </div>
      <div id="mVCWordsWrap" style="display:none">
        <div style="font-family:'Fredoka One',cursive;font-size:.72rem;color:#c0b8b0;text-transform:uppercase;letter-spacing:.8px;margin-bottom:8px;" id="mVCWordsLbl">Example Words</div>
        <div class="m-vc-words" id="mVCWords"></div>
      </div>
      <button class="m-sound-btn" id="mSndBtn" onclick="speakLetter()">
        <i class="fas fa-volume-up" id="mSndIco"></i>
        <span id="mSndLbl">Hear It!</span>
      </button>
    </div>
  </div>
</div>

</div><!-- /page-wrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
/* ============================================================
   DATA (from PHP)
   ============================================================ */
const DATA={
  en:<?php $o=[];foreach($letters_en as $l=>$d)$o[]=['letter'=>$l,'word'=>$d[0],'img'=>$d[1],'color'=>$d[2]];echo json_encode($o,JSON_UNESCAPED_UNICODE);?>,
  tl:<?php $o2=[];foreach($letters_tl as $l=>$d)$o2[]=['letter'=>$l,'word'=>$d[0],'img'=>$d[1],'color'=>$d[2]];echo json_encode($o2,JSON_UNESCAPED_UNICODE);?>
};
const VC_WORDS={
  en:<?=json_encode($vc_words_en,JSON_UNESCAPED_UNICODE)?>,
  tl:<?=json_encode($vc_words_tl,JSON_UNESCAPED_UNICODE)?>
};
const VOWELS=['A','E','I','O','U'];
const ALPHA_TL=<?=json_encode($alphabet_tl)?>;

const VC_COPY={
  en:{
    pageTitle:'Learn the Letters!',pageSub:'Tap a letter to explore it',
    badgeText:'English Alphabet — 26 Letters',
    vcTitle:'Vowels &amp; Consonants!',vcSub:'See how letters are divided — tap any letter!',
    vcGridLabel:'All Letters — Color Coded',vcGridSub:'Pink = Vowel · Blue = Consonant',
    vcVCount:'(5)',vcCCount:'(21)',vcLegV:'Vowel',vcLegC:'Consonant',
    vcVCBadgeV:'Vowel',vcVCBadgeC:'Consonant',mVCWordsLbl:'Example Words',
    songTitle:'Sing the ABC Song!',songSub:'Watch and sing along — tap a video to play it!',
    secLettersTitle:'Letters',secLettersSub:'Tap a letter card to explore it!',
    writeTitle:'Stroke by Stroke!',writeSub:'Choose a letter and watch how each stroke is made on the writing lines',
    pickerLabel:'Pick a letter',wbSubText:'Watch each stroke carefully',
    capLabel:'Capital',smallLabel:'Small',
    playLbl:'Watch me write!',resetLbl:'Reset',
    hintIdle:'Press Play to watch the stroke order',
    hintRun:'Watch the dot move across each stroke...',
    hintDone:'All strokes done! Press Watch again to replay.',
    songPhSub:'Tap to play the video',
    actDivider:'Now let\'s play!',
    matchTitle:'Picture Match!',matchSub:'Find the picture that starts with the letter!',
    matchInstruction:'Tap the picture that starts with this letter!',
    matchNext:'Next!',
    missingTitle:'What\'s the Missing Letter?',missingSub:'Fill in the blank — tap the right letter!',
    missingNext:'Next!',
    kwriteTitle:'Your Turn to Write!',kwriteSub:'A random letter will appear — write both capital and small!',
    kwritePromptLabel:'Write both letters below!',
    kwriteCapLabel:'Capital',kwriteSmallLabel:'Small',
    kwriteCheckLbl:'Check!',kwriteClearLbl:'Clear',
    kwriteTryAgainLbl:'Try Again',kwriteNextLbl:'Next Letter!',
    kwriteRef:'Use the faint letter as a guide — trace over it!',
    hearIt:'Hear It!',
  },
  tl:{
    pageTitle:'Alamin ang mga Titik!',pageSub:'I-tap ang titik para matuklasan ito',
    badgeText:'Alpabetong Filipino — 20 Letra',
    vcTitle:'Patinig at Katinig!',vcSub:'Tingnan kung paano nahahati ang mga titik — i-tap ang titik!',
    vcGridLabel:'Lahat ng Titik — May Kulay',vcGridSub:'Pink = Patinig · Asul = Katinig',
    vcVCount:'(5)',vcCCount:'(15)',vcLegV:'Patinig',vcLegC:'Katinig',
    vcVCBadgeV:'Patinig',vcVCBadgeC:'Katinig',mVCWordsLbl:'Halimbawa ng Salita',
    songTitle:'Kumanta ng ABC!',songSub:'Panoorin at kumanta — i-tap ang video para i-play!',
    secLettersTitle:'Mga Titik',secLettersSub:'I-tap ang titik para matuklasan ito!',
    writeTitle:'Sulat nang Sulat!',writeSub:'Pumili ng titik at panoorin kung paano isinusulat ito sa linya',
    pickerLabel:'Pumili ng titik',wbSubText:'Panoorin ang bawat stroke nang maingat',
    capLabel:'Malaking Titik',smallLabel:'Maliit na Titik',
    playLbl:'Panoorin ang pagsulat!',resetLbl:'I-reset',
    hintIdle:'Pindutin ang Play para panoorin ang stroke order',
    hintRun:'Panoorin ang tuldok habang gumagalaw...',
    hintDone:'Tapos na ang lahat ng stroke! Pindutin ulit para panoorin muli.',
    songPhSub:'I-tap para i-play ang video',
    actDivider:'Maglaro na tayo!',
    matchTitle:'Itugma ang Larawan!',matchSub:'Hanapin ang larawan na nagsisimula sa titik!',
    matchInstruction:'I-tap ang larawan na nagsisimula sa titik na ito!',
    matchNext:'Susunod!',
    missingTitle:'Ano ang Nawawalang Titik?',missingSub:'Punan ang blangko — i-tap ang tamang titik!',
    missingNext:'Susunod!',
    kwriteTitle:'Ikaw Naman ang Sumulat!',kwriteSub:'Lalabas ang random na titik — isulat ang malaki at maliit!',
    kwritePromptLabel:'Isulat ang dalawang titik sa ibaba!',
    kwriteCapLabel:'Malaking Titik',kwriteSmallLabel:'Maliit na Titik',
    kwriteCheckLbl:'Suriin!',kwriteClearLbl:'Burahin',
    kwriteTryAgainLbl:'Subukan Ulit',kwriteNextLbl:'Susunod na Titik!',
    kwriteRef:'Gamitin ang maliwanag na titik bilang gabay — trace ito!',
    hearIt:'Pakinggan!',
  }
};

let lang='en',curLetter='',curWord='',curColor='#FF6B6B';
let curModalMode='letter';
let curVCisVowel=false,curVCLetter='';
let wLetter='',wColor='#555',wWord='';
let traceRAF=null;
const VW=200,VH=260,LIFT=22,SPEED=0.42;

/* ============================================================
   WEB AUDIO — SOUND EFFECTS
   ============================================================ */
let _audioCtx=null;
function getAudioCtx(){
  if(!_audioCtx)_audioCtx=new(window.AudioContext||window.webkitAudioContext)();
  return _audioCtx;
}
function sfxCorrect(){
  try{
    const ctx=getAudioCtx();
    const notes=[523,659,784,1047];
    notes.forEach((freq,i)=>{
      const t=ctx.currentTime+i*0.12;
      const osc=ctx.createOscillator();
      const gain=ctx.createGain();
      osc.connect(gain);gain.connect(ctx.destination);
      osc.type='sine';osc.frequency.setValueAtTime(freq,t);
      gain.gain.setValueAtTime(0,t);
      gain.gain.linearRampToValueAtTime(0.32,t+0.03);
      gain.gain.exponentialRampToValueAtTime(0.001,t+0.35);
      osc.start(t);osc.stop(t+0.36);
    });
  }catch(e){}
}
function sfxWrong(){
  try{
    const ctx=getAudioCtx();
    const osc=ctx.createOscillator();
    const gain=ctx.createGain();
    osc.connect(gain);gain.connect(ctx.destination);
    osc.type='sawtooth';
    osc.frequency.setValueAtTime(180,ctx.currentTime);
    osc.frequency.linearRampToValueAtTime(100,ctx.currentTime+0.25);
    gain.gain.setValueAtTime(0.28,ctx.currentTime);
    gain.gain.exponentialRampToValueAtTime(0.001,ctx.currentTime+0.28);
    osc.start(ctx.currentTime);osc.stop(ctx.currentTime+0.29);
  }catch(e){}
}
function sfxFanfare(){
  try{
    const ctx=getAudioCtx();
    const melody=[523,523,523,659,523,659,784];
    const times= [0,  .12,.24, .38,.5, .6, .72];
    melody.forEach((freq,i)=>{
      const t=ctx.currentTime+times[i];
      const osc=ctx.createOscillator();
      const gain=ctx.createGain();
      osc.connect(gain);gain.connect(ctx.destination);
      osc.type='triangle';osc.frequency.setValueAtTime(freq,t);
      gain.gain.setValueAtTime(0,t);
      gain.gain.linearRampToValueAtTime(0.28,t+0.04);
      gain.gain.exponentialRampToValueAtTime(0.001,t+0.28);
      osc.start(t);osc.stop(t+0.29);
    });
  }catch(e){}
}

/* ============================================================
   SCREEN FLASH
   ============================================================ */
function screenFlash(type){
  const el=document.getElementById('screenFlash');
  el.className='';
  void el.offsetWidth;
  el.className='flash-'+type;
  el.addEventListener('animationend',()=>{el.className='';},{once:true});
}

/* ============================================================
   LANG / VOICE HELPERS
   ============================================================ */
function getLang(){return lang==='tl'?'fil-PH':'en-US';}
function isVowel(l){return VOWELS.includes(l.toUpperCase());}
function getAlphabet(l){return l==='en'?DATA.en.map(x=>x.letter):ALPHA_TL;}
function getVCWords(letter){return(VC_WORDS[lang][letter]||VC_WORDS[lang][letter.toUpperCase()]||[]);}

/* ============================================================
   PICK BEST VOICE FOR LANGUAGE
   Tagalog words are best read naturally — we prefer a Filipino
   voice but fall back gracefully. We NEVER force a non-native
   voice to speak Filipino text (that causes the choppy/robotic
   output). Rate is kept normal (not slowed to 0.55) so words
   flow smoothly instead of sounding cut-off between syllables.
   ============================================================ */
function getBestVoice(targetLang){
  const voices=window.speechSynthesis.getVoices();
  if(targetLang==='fil-PH'){
    // Priority: native Filipino > any fil > any tl > en-PH > any en
    return voices.find(v=>v.lang==='fil-PH')
      || voices.find(v=>v.lang.startsWith('fil'))
      || voices.find(v=>v.lang.startsWith('tl'))
      || voices.find(v=>v.lang==='en-PH')
      || voices.find(v=>v.lang.startsWith('en-PH'))
      || voices.find(v=>v.lang.startsWith('en'))
      || null;
  }
  return voices.find(v=>v.lang==='en-US')
    || voices.find(v=>v.lang.startsWith('en'))
    || null;
}

/* Speak with smooth, natural rate — not too slow (avoids choppy syllables) */
function makeSpeakUtterance(text, targetLang){
  const u = new SpeechSynthesisUtterance(text);

  const voices = speechSynthesis.getVoices();

  let selectedVoice;

  if(targetLang === 'fil-PH' || targetLang === 'tl'){
    // 🔥 PRIORITY: Filipino voice
    selectedVoice = voices.find(v =>
      v.lang.toLowerCase().includes('fil') ||
      v.lang.toLowerCase().includes('tl')
    );
  } else {
    // English fallback
    selectedVoice = voices.find(v =>
      v.lang.toLowerCase().includes('en')
    );
  }

  if(selectedVoice){
    u.voice = selectedVoice;
  }

  u.lang = targetLang;

  // 🔥 smoother + hindi putol
  u.rate = 0.9;
  u.pitch = 1;

  return u;
}

function applyLangUI(){
  const c=VC_COPY[lang];
  const e=lang==='en';
  document.getElementById('pageTitle').textContent=c.pageTitle;
  document.getElementById('pageSub').textContent=c.pageSub;
  document.getElementById('badgeText').innerHTML=e?'English Alphabet &mdash; 26 Letters':c.badgeText;
  document.getElementById('langBadge').classList.toggle('tl',!e);
  document.getElementById('secLettersTitle').textContent=c.secLettersTitle;
  document.getElementById('secLettersSub').textContent=c.secLettersSub;
  document.getElementById('writeTitle').textContent=c.writeTitle;
  document.getElementById('writeSub').textContent=c.writeSub;
  document.getElementById('pickerLabel').textContent=c.pickerLabel;
  document.getElementById('wbSubText').textContent=c.wbSubText;
  document.getElementById('capLabel').textContent=c.capLabel;
  document.getElementById('smallLabel').textContent=c.smallLabel;
  document.getElementById('playLbl').textContent=c.playLbl;
  document.getElementById('resetLbl').textContent=c.resetLbl;
  document.getElementById('wbHint').textContent=c.hintIdle;
  document.getElementById('vcTitle').innerHTML=c.vcTitle;
  document.getElementById('vcSub').textContent=c.vcSub;
  document.getElementById('vcGridLabel').textContent=c.vcGridLabel;
  document.getElementById('vcGridSub').textContent=c.vcGridSub;
  document.getElementById('vcVCount').textContent=c.vcVCount;
  document.getElementById('vcCCount').textContent=c.vcCCount;
  document.getElementById('vcLegV').textContent=c.vcLegV;
  document.getElementById('vcLegC').textContent=c.vcLegC;
  document.getElementById('songTitle').textContent=c.songTitle;
  document.getElementById('songSub').textContent=c.songSub;
  document.getElementById('songPhSub').textContent=c.songPhSub;
  document.getElementById('actDividerText').textContent=c.actDivider;
  document.getElementById('matchTitle').textContent=c.matchTitle;
  document.getElementById('matchSub').textContent=c.matchSub;
  document.getElementById('matchInstruction').textContent=c.matchInstruction;
  document.getElementById('matchNextLbl').textContent=c.matchNext;
  document.getElementById('missingTitle').textContent=c.missingTitle;
  document.getElementById('missingSub').textContent=c.missingSub;
  document.getElementById('missingNextLbl').textContent=c.missingNext;
  document.getElementById('kwriteTitle').textContent=c.kwriteTitle;
  document.getElementById('kwriteSub').textContent=c.kwriteSub;
  document.getElementById('kwritePromptLabel').textContent=c.kwritePromptLabel;
  document.getElementById('kwriteCapLabel').textContent=c.kwriteCapLabel;
  document.getElementById('kwriteSmallLabel').textContent=c.kwriteSmallLabel;
  document.getElementById('kwriteCheckLbl').textContent=c.kwriteCheckLbl;
  document.getElementById('kwriteClearLbl').textContent=c.kwriteClearLbl;
  document.getElementById('kwriteTryAgainLbl').textContent=c.kwriteTryAgainLbl;
  document.getElementById('kwriteNextLbl').textContent=c.kwriteNextLbl;
  document.getElementById('kwriteRef').textContent=c.kwriteRef;
}

function renderGrid(l){
  const grid=document.getElementById('lettersGrid');
  grid.style.opacity='0';grid.style.transform='scale(.96)';
  setTimeout(()=>{
    grid.innerHTML='';
    DATA[l].forEach((item,i)=>{
      const card=document.createElement('div');
      card.className='letter-card';
      card.style.cssText=`--cc:${item.color};animation-delay:${(i*.036).toFixed(3)}s`;
      card.onclick=e=>{ripple(e,card,item.color);openLetterModal(item);};
      card.innerHTML=`
        <div class="card-bar"></div>
        <div class="card-inner">
          <div class="card-letters">
            <span class="card-upper">${item.letter.toUpperCase()}</span>
            <span class="card-lower">${item.letter.toLowerCase()}</span>
          </div>
          <div class="card-sep"></div>
          <div class="card-word">${item.word}</div>
          <div class="card-photo">
            <img src="${item.img}" alt="${item.word}" loading="lazy"
                 onerror="this.closest('.card-photo').innerHTML='<div style=\'width:100%;height:100%;display:flex;align-items:center;justify-content:center;color:#ccc\'><i class=\'fas fa-image fa-lg\'></i></div>'">
          </div>
        </div>`;
      grid.appendChild(card);
    });
    grid.style.opacity='1';grid.style.transform='scale(1)';
    buildPicker(l);
    renderVC(l);
  },240);
}

function ripple(e,card,color){
  const r=document.createElement('span');r.className='ripple';
  const rc=card.getBoundingClientRect(),sz=Math.max(rc.width,rc.height);
  r.style.cssText=`width:${sz}px;height:${sz}px;left:${e.clientX-rc.left-sz/2}px;top:${e.clientY-rc.top-sz/2}px;background:${color}35;`;
  card.appendChild(r);r.addEventListener('animationend',()=>r.remove());
}

function setLang(l){
  if(l===lang)return;lang=l;
  document.getElementById('btnEN').classList.toggle('active',l==='en');
  document.getElementById('btnTL').classList.toggle('active',l==='tl');
  applyLangUI();
  document.getElementById('writeBoard').classList.remove('visible');
  wLetter='';cancelTrace();
  renderGrid(l);
  initSongSection();
  newMatchQ();
  newMissingQ();
  loadRandomKWriteLetter();
}

function renderVC(l){
  const alpha=getAlphabet(l);
  const grid=document.getElementById('vcAlphaGrid');
  grid.innerHTML='';
  const c=VC_COPY[l];
  alpha.forEach((letter,i)=>{
    const vow=isVowel(letter);
    const card=document.createElement('div');
    card.className=`vc-card ${vow?'vow':'con'}`;
    card.style.animationDelay=(i*.03).toFixed(3)+'s';
    card.innerHTML=`
      <div class="vc-cbar"></div>
      <div class="vc-cinner">
        <span class="vc-cu">${letter.toUpperCase()}</span>
        <span class="vc-cl">${letter.toLowerCase()}</span>
        <span class="vc-cbadge">${vow?c.vcVCBadgeV:c.vcVCBadgeC}</span>
      </div>`;
    card.onclick=()=>openVCModal(letter,vow);
    grid.appendChild(card);
  });
}

function openLetterModal(item){
  curModalMode='letter';
  curLetter=item.letter;curWord=item.word;curColor=item.color;
  const box=document.getElementById('mBox');
  box.style.setProperty('--mc',item.color);
  document.getElementById('mStripe').style.background=item.color;
  document.getElementById('mWm').textContent=item.letter;
  document.getElementById('mU').textContent=item.letter.toUpperCase();
  document.getElementById('mL').textContent=item.letter.toLowerCase();
  document.getElementById('mWord').textContent=item.word;
  document.getElementById('mWord').style.display='';
  document.getElementById('mPhoto').style.display='';
  document.getElementById('mVCBadge').style.display='none';
  document.getElementById('mVCWordsWrap').style.display='none';
  document.getElementById('mSndBtn').style.background=item.color;
  const img=document.getElementById('mImg');img.src=item.img;img.alt=item.word;
  const ml=box.querySelector('.modal-letters-big');
  ml.style.animation='none';ml.offsetHeight;ml.style.animation='';
  document.getElementById('mOverlay').classList.add('active');
  launchConfetti(item.color);
  setTimeout(speakLetter,380);
}

function openVCModal(letter,vow){
  curModalMode='vc';
  curVCLetter=letter;curVCisVowel=vow;curLetter=letter;
  const hexColor=vow?'#e8336d':'#1a6de8';
  const cls=vow?'vow':'con';
  const c=VC_COPY[lang];
  const box=document.getElementById('mBox');
  box.style.setProperty('--mc',hexColor);
  document.getElementById('mStripe').style.background=hexColor;
  document.getElementById('mWm').textContent=letter.toUpperCase();
  document.getElementById('mU').textContent=letter.toUpperCase();
  document.getElementById('mL').textContent=letter.toLowerCase();
  document.getElementById('mWord').style.display='none';
  document.getElementById('mPhoto').style.display='none';
  const badge=document.getElementById('mVCBadge');
  badge.className=`m-vc-badge ${cls}`;badge.style.display='inline-flex';
  badge.querySelector('i').className=vow?'fas fa-star':'fas fa-music';
  document.getElementById('mVCType').textContent=vow?c.vcVCBadgeV:c.vcVCBadgeC;
  document.getElementById('mVCWordsLbl').textContent=c.mVCWordsLbl;
  document.getElementById('mVCWordsWrap').style.display='';
  const words=getVCWords(letter);
  const mw=document.getElementById('mVCWords');mw.innerHTML='';
  words.forEach(w=>{
    const chip=document.createElement('span');chip.className=`m-vc-chip ${cls}`;chip.textContent=w;
    chip.onclick=()=>speakWord(w);mw.appendChild(chip);
  });
  document.getElementById('mSndBtn').style.background=hexColor;
  const ml=box.querySelector('.modal-letters-big');
  ml.style.animation='none';ml.offsetHeight;ml.style.animation='';
  document.getElementById('mOverlay').classList.add('active');
  launchConfetti(hexColor);
  setTimeout(speakLetter,350);
}

function closeM(){document.getElementById('mOverlay').classList.remove('active');window.speechSynthesis?.cancel();}
function closeMBg(e){if(e.target===document.getElementById('mOverlay'))closeM();}

/* ============================================================
   TTS — letter modal only
   FIX: natural rate (0.85 not 0.55) + smarter voice selection
   so Filipino words are not choppy/robotic.
   ============================================================ */
function speakLetter(){
  if(!window.speechSynthesis)return;
  window.speechSynthesis.cancel();
  const btn=document.getElementById('mSndBtn'),
        ico=document.getElementById('mSndIco'),
        lbl=document.getElementById('mSndLbl');
  ico.className='fas fa-spinner fa-spin';lbl.textContent='...';btn.disabled=true;
  const restore=()=>{
    ico.className='fas fa-volume-up';
    lbl.textContent=VC_COPY[lang].hearIt;
    btn.disabled=false;
  };

  function doSpeak(){
    const targetLang=getLang();
    const voice=getBestVoice(targetLang);

    // Speak the letter name first
    const lU=makeSpeakUtterance(curLetter.toUpperCase(),targetLang,voice);

    if(curModalMode==='letter'&&curWord){
      lU.onend=()=>{
        setTimeout(()=>{
          const wU=makeSpeakUtterance(curWord,targetLang,voice);
          wU.onend=restore;
          wU.onerror=restore;
          window.speechSynthesis.speak(wU);
        },280);
      };
      lU.onerror=restore;
      window.speechSynthesis.speak(lU);
    } else {
      lU.onend=restore;
      lU.onerror=restore;
      window.speechSynthesis.speak(lU);
    }
  }

  // Make sure voices are loaded before speaking
  if(window.speechSynthesis.getVoices().length>0){
    doSpeak();
  } else {
    window.speechSynthesis.onvoiceschanged=()=>{
      window.speechSynthesis.onvoiceschanged=null;
      doSpeak();
    };
  }
}

function speakWord(w){
  if(!window.speechSynthesis)return;
  window.speechSynthesis.cancel();
  function doSpeak(){
    const targetLang=getLang();
    const voice=getBestVoice(targetLang);
    const u=makeSpeakUtterance(w,targetLang,voice);
    window.speechSynthesis.speak(u);
  }
  if(window.speechSynthesis.getVoices().length>0){doSpeak();}
  else{window.speechSynthesis.onvoiceschanged=()=>{window.speechSynthesis.onvoiceschanged=null;doSpeak();};}
}



function launchConfetti(color){
  const cols=[color,'#FFE66D','#FF6B6B','#A29BFE','#4ECDC4','#FD79A8','#FDCB6E'];
  for(let i=0;i<38;i++){
    const p=document.createElement('div');p.className='cfbit';
    p.style.cssText=`left:${Math.random()*100}vw;top:-12px;background:${cols[0|Math.random()*cols.length]};border-radius:${Math.random()>.5?'50%':'3px'};width:${6+Math.random()*8}px;height:${6+Math.random()*8}px;animation-duration:${1.2+Math.random()*1.5}s;animation-delay:${Math.random()*.5}s;`;
    document.body.appendChild(p);p.addEventListener('animationend',()=>p.remove());
  }
}
function launchConfettiBig(color){
  const cols=[color,'#FFE66D','#FF6B6B','#A29BFE','#4ECDC4','#FD79A8','#FDCB6E','#6BCB77'];
  for(let i=0;i<65;i++){
    const p=document.createElement('div');p.className='cfbit';
    p.style.cssText=`left:${Math.random()*100}vw;top:-12px;background:${cols[0|Math.random()*cols.length]};border-radius:${Math.random()>.5?'50%':'3px'};width:${7+Math.random()*10}px;height:${7+Math.random()*10}px;animation-duration:${1.0+Math.random()*1.8}s;animation-delay:${Math.random()*.4}s;`;
    document.body.appendChild(p);p.addEventListener('animationend',()=>p.remove());
  }
}

function buildPicker(l){
  const el=document.getElementById('letterPicker');el.innerHTML='';
  DATA[l].forEach(item=>{
    const btn=document.createElement('div');
    btn.className='pick-key';btn.style.setProperty('--pc',item.color);
    btn.innerHTML=`<span class="pk-u">${item.letter.toUpperCase()}</span><span class="pk-l">${item.letter.toLowerCase()}</span>`;
    btn.onclick=()=>selectWrite(item,btn);
    el.appendChild(btn);
  });
}

function selectWrite(item,btn){
  document.querySelectorAll('.pick-key').forEach(b=>b.classList.remove('sel'));
  btn.classList.add('sel');
  wLetter=item.letter;wColor=item.color;wWord=item.word;
  document.getElementById('wbU').textContent=item.letter.toUpperCase();
  document.getElementById('wbL').textContent=item.letter.toLowerCase();
  document.getElementById('wbWord').textContent=item.word;
  const board=document.getElementById('writeBoard');
  board.style.setProperty('--wbc',item.color);
  document.getElementById('btnPlay').style.background=item.color;
  buildSVGs(item.color);setPlayBtn('idle');
  setHint(VC_COPY[lang].hintIdle);
  board.classList.remove('visible');void board.offsetWidth;board.classList.add('visible');
}

function mkEl(tag){return document.createElementNS('http://www.w3.org/2000/svg',tag);}
function buildSVGs(color){
  cancelTrace();
  ['U','L'].forEach(w=>{
    const svg=document.getElementById('svg'+w);svg.innerHTML='';
    for(let y=35;y<VH;y+=35){
      if(y===50||y===120||y===190)continue;
      const rl=mkEl('line');rl.setAttribute('x1',0);rl.setAttribute('y1',y);rl.setAttribute('x2',VW);rl.setAttribute('y2',y);
      rl.setAttribute('stroke','#ddd8c8');rl.setAttribute('stroke-width','0.8');rl.setAttribute('opacity','0.5');svg.appendChild(rl);
    }
    const lt=mkEl('line');lt.setAttribute('x1',30);lt.setAttribute('y1',50);lt.setAttribute('x2',VW);lt.setAttribute('y2',50);lt.setAttribute('stroke','#4a90d9');lt.setAttribute('stroke-width','1.6');lt.setAttribute('opacity','.65');svg.appendChild(lt);
    const lm=mkEl('line');lm.setAttribute('x1',30);lm.setAttribute('y1',120);lm.setAttribute('x2',VW);lm.setAttribute('y2',120);lm.setAttribute('stroke','#d94040');lm.setAttribute('stroke-width','1.6');lm.setAttribute('opacity','.65');svg.appendChild(lm);
    const lb=mkEl('line');lb.setAttribute('x1',30);lb.setAttribute('y1',190);lb.setAttribute('x2',VW);lb.setAttribute('y2',190);lb.setAttribute('stroke','#4a90d9');lb.setAttribute('stroke-width','1.6');lb.setAttribute('opacity','.65');svg.appendChild(lb);
    const mh=155;
    const dh=mkEl('line');dh.setAttribute('x1',30);dh.setAttribute('y1',mh);dh.setAttribute('x2',VW);dh.setAttribute('y2',mh);dh.setAttribute('stroke','#c8b898');dh.setAttribute('stroke-width','0.8');dh.setAttribute('stroke-dasharray','5,5');dh.setAttribute('opacity','.45');svg.appendChild(dh);
    const labels=w==='U'?[{y:54,t:'T',c:'#4a90d9'},{y:194,t:'B',c:'#4a90d9'}]:[{y:124,t:'m',c:'#d94040'},{y:194,t:'b',c:'#4a90d9'}];
    labels.forEach(({y,t,c})=>{const lbl=mkEl('text');lbl.setAttribute('x','14');lbl.setAttribute('y',String(y));lbl.setAttribute('font-family','Nunito,sans-serif');lbl.setAttribute('font-weight','900');lbl.setAttribute('font-size','9');lbl.setAttribute('fill',c);lbl.setAttribute('opacity','.6');lbl.setAttribute('text-anchor','middle');lbl.textContent=t;svg.appendChild(lbl);});
    const key=lookupKey(wLetter);const stData=ST[key];
    if(stData){const segs=w==='U'?stData.U:stData.L;segs.forEach(seg=>{const g=mkEl('path');g.setAttribute('d',seg);g.setAttribute('fill','none');g.setAttribute('stroke',color);g.setAttribute('stroke-opacity','.1');g.setAttribute('stroke-width','9');g.setAttribute('stroke-linecap','round');g.setAttribute('stroke-linejoin','round');svg.appendChild(g);});}
  });
}

function setPlayBtn(s){
  const btn=document.getElementById('btnPlay'),ico=document.getElementById('playIco'),lbl=document.getElementById('playLbl');
  const c=VC_COPY[lang];
  if(s==='idle'){ico.className='fas fa-play';lbl.textContent=c.playLbl;btn.disabled=false;}
  else if(s==='run'){ico.className='fas fa-spinner fa-spin';lbl.textContent='...';btn.disabled=true;}
  else if(s==='done'){ico.className='fas fa-redo';lbl.textContent=c.playLbl;btn.disabled=false;}
}
function setHint(t){document.getElementById('wbHint').textContent=t;}
function doTrace(){
  if(!wLetter)return;
  cancelTrace();buildSVGs(wColor);setPlayBtn('run');setHint(VC_COPY[lang].hintRun);
  const key=lookupKey(wLetter);const stData=ST[key];
  if(!stData){setPlayBtn('idle');return;}
  let done=0;const onDone=()=>{done++;if(done>=2){setPlayBtn('done');setHint(VC_COPY[lang].hintDone);}};
  animStrokes('U',stData.U,wColor,onDone);animStrokes('L',stData.L,wColor,onDone);
}
function resetTrace(){if(!wLetter)return;cancelTrace();buildSVGs(wColor);setPlayBtn('idle');setHint(VC_COPY[lang].hintIdle);}
function cancelTrace(){if(traceRAF){cancelAnimationFrame(traceRAF);traceRAF=null;}}
function lookupKey(l){return l.length===1?l.toUpperCase():l.charAt(0).toUpperCase()+l.slice(1);}

function animStrokes(which,strokes,color,onDone){
  const svg=document.getElementById('svg'+which);
  const nums=['1','2','3','4','5'];
  strokes.forEach((seg,si)=>{
    const m=seg.match(/M\s*([\d.]+)\s*([\d.]+)/);if(!m)return;
    const lbl=mkEl('text');lbl.setAttribute('x',parseFloat(m[1])+13);lbl.setAttribute('y',parseFloat(m[2])-9);lbl.setAttribute('font-family','Nunito,sans-serif');lbl.setAttribute('font-weight','900');lbl.setAttribute('font-size','11');lbl.setAttribute('fill',color);lbl.setAttribute('opacity','.5');lbl.textContent=nums[si]||'';svg.appendChild(lbl);
  });
  const{pts}=flattenAll(strokes,1.3,LIFT);if(!pts.length){onDone?.();return;}
  const trail=mkEl('path');trail.setAttribute('fill','none');trail.setAttribute('stroke',color);trail.setAttribute('stroke-width','8');trail.setAttribute('stroke-linecap','round');trail.setAttribute('stroke-linejoin','round');trail.setAttribute('opacity','.8');svg.appendChild(trail);
  const ring=mkEl('circle');ring.setAttribute('r','15');ring.setAttribute('fill','none');ring.setAttribute('stroke',color);ring.setAttribute('stroke-width','3');ring.setAttribute('opacity','.22');svg.appendChild(ring);
  const dot=mkEl('circle');dot.setAttribute('r','7');dot.setAttribute('fill',color);dot.setAttribute('opacity','.95');svg.appendChild(dot);
  const sc=mkEl('circle');sc.setAttribute('cx',pts[0].x);sc.setAttribute('cy',pts[0].y);sc.setAttribute('r','11');sc.setAttribute('fill',color);sc.setAttribute('opacity','.18');svg.insertBefore(sc,trail);
  let idx=0,accum=0,trailD='';
  function step(){
    accum+=SPEED;
    while(accum>=1&&idx<pts.length-1){idx++;accum--;}
    const p=pts[idx];
    dot.setAttribute('cx',p.x);dot.setAttribute('cy',p.y);ring.setAttribute('cx',p.x);ring.setAttribute('cy',p.y);
    if(!p.lift){const prev=pts[idx-1];if(idx===0||prev?.lift)trailD+=`M ${p.x} ${p.y} `;else trailD+=`L ${p.x} ${p.y} `;trail.setAttribute('d',trailD);}
    if(idx<pts.length-1){traceRAF=requestAnimationFrame(step);}
    else{dot.setAttribute('r','11');setTimeout(()=>{dot.setAttribute('r','7');onDone?.();},400);}
  }
  traceRAF=requestAnimationFrame(step);
}

function flattenAll(strokes,step,lift){
  const pts=[];
  strokes.forEach(seg=>{
    const cmds=parsePath(seg);let cx=0,cy=0;
    cmds.forEach(cmd=>{
      if(cmd.type==='M'){if(pts.length>0){const last=pts[pts.length-1];for(let g=0;g<lift;g++)pts.push({x:last.x,y:last.y,lift:true});}cx=cmd.x;cy=cmd.y;pts.push({x:cx,y:cy});}
      else if(cmd.type==='L'){sampleLine(cx,cy,cmd.x,cmd.y,step).forEach(p=>pts.push(p));cx=cmd.x;cy=cmd.y;}
      else if(cmd.type==='C'){sampleCubic(cx,cy,cmd.x1,cmd.y1,cmd.x2,cmd.y2,cmd.x,cmd.y,step).forEach(p=>pts.push(p));cx=cmd.x;cy=cmd.y;}
    });
  });
  return{pts};
}
function parsePath(d){
  const cmds=[],re=/([MLHVCSQTA])\s*([-\d.,\s]*)/gi;let m;
  while((m=re.exec(d))!==null){
    const t=m[1].toUpperCase(),a=m[2].trim().split(/[\s,]+/).filter(Boolean).map(Number);
    if(t==='M')cmds.push({type:'M',x:a[0],y:a[1]});
    else if(t==='L')cmds.push({type:'L',x:a[0],y:a[1]});
    else if(t==='C')cmds.push({type:'C',x1:a[0],y1:a[1],x2:a[2],y2:a[3],x:a[4],y:a[5]});
  }
  return cmds;
}
function sampleLine(x0,y0,x1,y1,step){const pts=[],n=Math.max(1,Math.ceil(Math.hypot(x1-x0,y1-y0)/step));for(let i=1;i<=n;i++)pts.push({x:x0+(x1-x0)*i/n,y:y0+(y1-y0)*i/n});return pts;}
function sampleCubic(x0,y0,x1,y1,x2,y2,x3,y3,step){const n=Math.max(2,Math.ceil(Math.hypot(x3-x0,y3-y0)*2/step)),pts=[];for(let i=1;i<=n;i++){const t=i/n,mt=1-t;pts.push({x:mt*mt*mt*x0+3*mt*mt*t*x1+3*mt*t*t*x2+t*t*t*x3,y:mt*mt*mt*y0+3*mt*mt*t*y1+3*mt*t*t*y2+t*t*t*y3});}return pts;}

/* Stroke definitions */
const T=50,M=120,B=190,D=235;
const ST={
  A:{U:["M 100 50 L 48 190","M 100 50 L 152 190","M 66 138 L 134 138"],L:["M 138 155 C 138 120 60 120 60 155 C 60 190 100 192 138 178","M 138 120 L 138 190"]},
  B:{U:["M 52 50 L 52 190","M 52 50 C 120 50 125 105 80 120 C 130 120 132 192 52 190"],L:["M 55 50 L 55 190","M 55 155 C 55 120 148 120 148 155 C 148 190 55 190 55 155"]},
  C:{U:["M 158 72 C 95 28 30 60 30 120 C 30 180 95 210 158 168"],L:["M 150 140 C 96 112 35 128 35 155 C 35 182 96 198 150 180"]},
  D:{U:["M 52 50 L 52 190","M 52 50 C 172 50 172 190 52 190"],L:["M 62 155 C 62 120 142 120 142 155 C 142 190 62 190 62 155","M 142 50 L 142 190"]},
  E:{U:["M 155 50 L 42 50 L 42 190 L 155 190","M 42 120 L 128 120"],L:["M 38 152 L 152 152 C 152 118 38 108 38 152 C 38 188 152 196 152 178"]},
  F:{U:["M 50 50 L 50 190","M 50 50 L 162 50","M 50 120 L 138 120"],L:["M 128 52 C 162 52 168 80 140 90 L 62 90 L 62 190","M 62 120 L 132 120"]},
  G:{U:["M 158 72 C 95 28 30 60 30 120 C 30 180 95 212 158 168 L 158 120 L 108 120"],L:["M 145 140 C 96 112 35 128 35 155 C 35 182 96 198 145 178","M 145 120 L 145 190 C 145 222 95 232 65 218"]},
  H:{U:["M 46 50 L 46 190","M 154 50 L 154 190","M 46 120 L 154 120"],L:["M 50 50 L 50 190","M 50 140 C 72 120 152 120 152 145 L 152 190"]},
  I:{U:["M 65 50 L 135 50","M 100 50 L 100 190","M 65 190 L 135 190"],L:["M 100 108 L 100 122","M 100 132 L 100 190"]},
  J:{U:["M 135 50 L 135 155 C 135 188 42 192 42 162"],L:["M 112 108 L 112 122","M 112 132 L 112 200 C 112 228 42 232 42 205"]},
  K:{U:["M 48 50 L 48 190","M 152 50 L 48 120","M 76 138 L 158 190"],L:["M 52 50 L 52 190","M 145 120 L 52 158","M 80 146 L 152 190"]},
  L:{U:["M 48 50 L 48 190 L 158 190"],L:["M 100 50 L 100 190"]},
  M:{U:["M 35 190 L 35 50 L 100 125 L 165 50 L 165 190"],L:["M 42 120 L 42 190","M 42 138 C 60 118 108 118 108 140 L 108 190","M 108 140 C 126 118 168 118 168 140 L 168 190"]},
  N:{U:["M 40 190 L 40 50 L 160 190 L 160 50"],L:["M 46 120 L 46 190","M 46 138 C 65 118 152 118 152 142 L 152 190"]},
  O:{U:["M 100 50 C 28 50 26 190 100 190 C 174 190 174 50 100 50"],L:["M 100 120 C 42 120 42 190 100 190 C 158 190 158 120 100 120"]},
  P:{U:["M 48 50 L 48 190","M 48 50 C 158 50 158 120 48 120"],L:["M 62 120 L 62 235","M 62 120 C 155 100 155 190 62 178"]},
  Q:{U:["M 100 50 C 28 50 26 190 100 190 C 174 190 174 50 100 50","M 132 168 L 168 200"],L:["M 100 120 C 38 120 38 190 100 190 C 162 190 162 120 100 120","M 145 120 L 145 235"]},
  R:{U:["M 48 50 L 48 190","M 48 50 C 155 50 155 120 48 120","M 82 120 L 162 190"],L:["M 55 120 L 55 190","M 55 140 C 75 115 145 118 135 142"]},
  S:{U:["M 158 72 C 85 30 18 78 72 122 C 125 165 182 200 95 238"],L:["M 148 132 C 88 108 32 130 68 155 C 105 180 162 175 128 198 C 92 220 32 205 28 185"]},
  T:{U:["M 25 50 L 175 50","M 100 50 L 100 190"],L:["M 38 98 L 148 98","M 93 52 L 93 190"]},
  U:{U:["M 40 50 L 40 145 C 40 192 160 192 160 145 L 160 50"],L:["M 48 120 L 48 170 C 48 198 152 198 152 170 L 152 120 L 152 190"]},
  V:{U:["M 28 50 L 100 190 L 172 50"],L:["M 40 120 L 100 190 L 160 120"]},
  W:{U:["M 22 50 L 56 190 L 100 112 L 144 190 L 178 50"],L:["M 28 120 L 58 190 L 100 145 L 142 190 L 172 120"]},
  X:{U:["M 32 50 L 168 190","M 168 50 L 32 190"],L:["M 40 120 L 162 190","M 162 120 L 40 190"]},
  Y:{U:["M 28 50 L 100 120 L 172 50","M 100 120 L 100 190"],L:["M 40 120 L 100 165 L 160 120","M 100 165 L 86 210 C 70 238 28 232 28 210"]},
  Z:{U:["M 32 50 L 168 50 L 32 190 L 168 190"],L:["M 40 120 L 162 120 L 40 190 L 162 190"]},
  Ng:{U:["M 40 190 L 40 50 L 160 190 L 160 50","M 160 120 C 178 95 200 120 188 142"],L:["M 46 120 L 46 190","M 46 138 C 65 118 152 118 152 142 L 152 190","M 152 142 C 165 122 188 135 180 158"]}
};

/* ============================================================
   SONG SECTION
   ============================================================ */
const SONG_VIDEOS={
  en:[
    {label:'Classic ABC',icon:'fa-music',ytId:'71h8MZshGSs',title:'ABC Song + More Nursery Rhymes & Kids Songs - CoComelon'},
    {label:'ABC Phonics',icon:'fa-volume-up',ytId:'36IBDpTRVNE',title:'ABC Phonics Song'},
    {label:'Super Simple',icon:'fa-star',ytId:'hq3yfQnllfQ',title:'Super Simple ABC'},
  ],
  tl:[
    {label:'ABC Song',icon:'fa-music',ytId:'75p-N9YKqNo',title:'ABC Song (English)'},
    {label:'Filipino ABC',icon:'fa-flag',ytId:'VpN0kbFIAKE',title:'Filipino Alphabet'},
    {label:'Phonics',icon:'fa-volume-up',ytId:'36IBDpTRVNE',title:'ABC Phonics Song'},
  ]
};
let songVideoIdx=0;
function buildSongTabs(){
  const el=document.getElementById('songVTabs');el.innerHTML='';
  SONG_VIDEOS[lang].forEach((v,i)=>{
    const btn=document.createElement('button');
    btn.className='svtab'+(i===songVideoIdx?' active':'');
    btn.innerHTML=`<i class="fas ${v.icon}"></i> ${v.label}`;
    btn.onclick=()=>selectSongVideo(i);
    el.appendChild(btn);
  });
}
function selectSongVideo(i){
  songVideoIdx=i;
  document.querySelectorAll('.svtab').forEach((b,idx)=>b.classList.toggle('active',idx===i));
  const v=SONG_VIDEOS[lang][i];
  document.getElementById('songPhTitle').textContent=v.title;
  document.getElementById('songYtPlaceholder').classList.remove('gone');
  const f=document.getElementById('songYtFrame');f.src='';f.style.display='none';
}
function loadSongVideo(){
  const v=SONG_VIDEOS[lang][songVideoIdx];
  const f=document.getElementById('songYtFrame');
  f.src=`https://www.youtube.com/embed/${v.ytId}?autoplay=1&rel=0&modestbranding=1`;
  f.style.display='block';
  document.getElementById('songYtPlaceholder').classList.add('gone');
}
function initSongSection(){
  songVideoIdx=0;buildSongTabs();
  document.getElementById('songPhTitle').textContent=SONG_VIDEOS[lang][0].title;
  document.getElementById('songYtPlaceholder').classList.remove('gone');
  const f=document.getElementById('songYtFrame');f.src='';f.style.display='none';
}

/* ============================================================
   ACTIVITY 1 — PICTURE MATCH
   ============================================================ */
function newMatchQ(){
  const c=VC_COPY[lang];
  const pool=DATA[lang];
  const correctIdx=Math.floor(Math.random()*pool.length);
  const correct=pool[correctIdx];
  const col=correct.color;
  let distIdxs=[];
  while(distIdxs.length<3){
    const r=Math.floor(Math.random()*pool.length);
    if(r!==correctIdx&&!distIdxs.includes(r))distIdxs.push(r);
  }
  const distractors=distIdxs.map(i=>pool[i]);
  const choices=[{item:correct,isCorrect:true},...distractors.map(d=>({item:d,isCorrect:false}))];
  choices.sort(()=>Math.random()-.5);
  document.getElementById('matchLetterBig').textContent=correct.letter.toUpperCase();
  document.getElementById('matchLetterBig').style.color=col;
  document.getElementById('matchInstruction').textContent=c.matchInstruction;
  hideNextBtn('matchNextBtn');
  const grid=document.getElementById('matchPicsGrid');grid.innerHTML='';
  choices.forEach(({item,isCorrect})=>{
    const card=document.createElement('div');card.className='match-pic-card';
    card.innerHTML=`
      <div class="match-pic-img">
        <img src="${item.img}" alt="${item.word}"
             onerror="this.style.display='none';this.closest('.match-pic-img').style.background='#f0ece4'">
      </div>
      <div class="match-pic-label">${item.word}</div>`;
    card.onclick=()=>checkMatchDirect(card,isCorrect,col,c);
    grid.appendChild(card);
  });
}

function checkMatchDirect(card,isCorrect,col){
  const allCards=document.querySelectorAll('.match-pic-card');
  allCards.forEach(ca=>ca.classList.add('disabled'));
  if(isCorrect){
    card.classList.add('correct');
    sfxCorrect();
    screenFlash('correct');
    launchConfettiBig(col);
    showNextBtn('matchNextBtn',col);
  } else {
    card.classList.add('wrong');
    sfxWrong();
    screenFlash('wrong');
    setTimeout(()=>{
      card.classList.remove('wrong','disabled');
      allCards.forEach(ca=>{if(ca!==card)ca.classList.remove('disabled');});
    },700);
  }
}
function nextMatchQ(){newMatchQ();}

/* ============================================================
   ACTIVITY 2 — MISSING LETTER
   ============================================================ */
let missingCorrect='';
function newMissingQ(){
  const alpha=getAlphabet(lang);
  const startIdx=Math.floor(Math.random()*(alpha.length-4));
  const window4=alpha.slice(startIdx,startIdx+4);
  const blankPos=1+Math.floor(Math.random()*2);
  missingCorrect=window4[blankPos];
  const row=document.getElementById('missingRow');row.innerHTML='';
  const dataItem=DATA[lang].find(x=>x.letter===missingCorrect);
  const actColor=dataItem?dataItem.color:'#A29BFE';
  row.style.setProperty('--act-color',actColor);
  window4.forEach((letter,i)=>{
    const box=document.createElement('div');
    if(i===blankPos){
      box.className='ml-box blank';box.id='blankBox';box.textContent='?';
    } else {
      box.className='ml-box given';box.textContent=letter.toUpperCase();
    }
    row.appendChild(box);
  });
  const others=alpha.filter(x=>x!==missingCorrect).sort(()=>Math.random()-.5).slice(0,3);
  const choices=[missingCorrect,...others].sort(()=>Math.random()-.5);
  const el=document.getElementById('mlChoices');el.innerHTML='';
  choices.forEach(letter=>{
    const btn=document.createElement('div');btn.className='ml-choice';
    btn.style.setProperty('--act-color',actColor);
    btn.textContent=letter.toUpperCase();
    btn.onclick=()=>checkMissing(btn,letter,actColor);
    el.appendChild(btn);
  });
  hideNextBtn('missingNextBtn');
}

function checkMissing(btn,letter,color){
  const allBtns=document.querySelectorAll('.ml-choice');
  if(letter===missingCorrect){
    btn.classList.add('correct');
    document.getElementById('blankBox').textContent=letter.toUpperCase();
    document.getElementById('blankBox').style.background=color+'22';
    document.getElementById('blankBox').style.borderColor=color;
    document.getElementById('blankBox').style.color=color;
    allBtns.forEach(b=>b.style.pointerEvents='none');
    sfxCorrect();
    screenFlash('correct');
    launchConfettiBig(color);
    showNextBtn('missingNextBtn',color);
  } else {
    btn.classList.add('wrong');
    sfxWrong();
    screenFlash('wrong');
    setTimeout(()=>{btn.classList.remove('wrong');},700);
  }
}
function nextMissingQ(){newMissingQ();}

/* ============================================================
   ACTIVITY 3 — KID WRITING
   ============================================================ */
let kwriteSelectedLetter='';
let kwriteColor='#00ACC1';
let kwriteCurrentWord='';

const BG_R=255,BG_G=254,BG_B=248;
const BG_TOL=30;
const CANVAS_W=200,CANVAS_H=220;

/*
  LENIENT INK THRESHOLD — kinder kids draw lightly / imperfectly.
  We only need a tiny bit of ink to confirm they drew something.
  Was 600px before; now 120px so even light strokes count.
*/
const MIN_INK=120;

const drawnStrokes={U:[],L:[]};

function loadRandomKWriteLetter(){
  const pool=DATA[lang];
  const item=pool[Math.floor(Math.random()*pool.length)];
  kwriteSelectedLetter=item.letter;
  kwriteColor=item.color;
  kwriteCurrentWord=item.word;

  document.getElementById('kwriteBannerLetter').textContent=
    item.letter.toUpperCase()+' / '+item.letter.toLowerCase();
  document.getElementById('kwriteBannerLetter').style.color=item.color;
  document.getElementById('kwriteBannerWord').textContent=item.word;
  document.getElementById('kwritePromptLabel').textContent=VC_COPY[lang].kwritePromptLabel;
  document.getElementById('kwriteCheckBtn').style.background=item.color;
  document.getElementById('guideU').textContent=item.letter.toUpperCase();
  document.getElementById('guideL').textContent=item.letter.toLowerCase();

  drawnStrokes.U=[];
  drawnStrokes.L=[];
  hideKWriteEmojiPop();
  hideKWriteActionBtns();

  drawRuledLines('canvasU',item.color);
  drawRuledLines('canvasL',item.color);
  setupCanvas('canvasU','U');
  setupCanvas('canvasL','L');
}

function drawRuledLines(canvasId,color){
  const canvas=document.getElementById(canvasId);
  if(!canvas)return;
  const ctx=canvas.getContext('2d');
  ctx.clearRect(0,0,CANVAS_W,CANVAS_H);
  ctx.fillStyle=`rgb(${BG_R},${BG_G},${BG_B})`;
  ctx.fillRect(0,0,CANVAS_W,CANVAS_H);
  const lineY=[40,80,120,160,200];
  lineY.forEach((y,i)=>{
    ctx.beginPath();ctx.moveTo(0,y);ctx.lineTo(CANVAS_W,y);
    if(i===1){ctx.strokeStyle='rgba(74,144,217,.5)';ctx.lineWidth=1.4;ctx.setLineDash([]);}
    else if(i===3){ctx.strokeStyle='rgba(217,64,64,.45)';ctx.lineWidth=1.4;ctx.setLineDash([]);}
    else{ctx.strokeStyle='rgba(200,190,170,.45)';ctx.lineWidth=0.8;ctx.setLineDash([4,4]);}
    ctx.stroke();ctx.setLineDash([]);
  });
}

function setupCanvas(canvasId,which){
  const canvas=document.getElementById(canvasId);
  if(!canvas)return;
  const newCanvas=canvas.cloneNode(true);
  canvas.parentNode.replaceChild(newCanvas,canvas);
  drawRuledLines(canvasId,kwriteColor);

  document.getElementById('guide'+which).textContent=
    which==='U'?kwriteSelectedLetter.toUpperCase():kwriteSelectedLetter.toLowerCase();

  drawnStrokes[which]=[];

  const ctx=newCanvas.getContext('2d');
  ctx.strokeStyle=kwriteColor;
  ctx.lineWidth=6;ctx.lineCap='round';ctx.lineJoin='round';

  let drawing=false,lastX=0,lastY=0;
  let currentStroke=[];

  function getPos(e){
    const rect=newCanvas.getBoundingClientRect();
    const scaleX=CANVAS_W/rect.width,scaleY=CANVAS_H/rect.height;
    if(e.touches)return{x:(e.touches[0].clientX-rect.left)*scaleX,y:(e.touches[0].clientY-rect.top)*scaleY};
    return{x:(e.clientX-rect.left)*scaleX,y:(e.clientY-rect.top)*scaleY};
  }
  function startDraw(e){e.preventDefault();drawing=true;const pos=getPos(e);lastX=pos.x;lastY=pos.y;currentStroke=[{x:pos.x,y:pos.y}];ctx.beginPath();ctx.moveTo(lastX,lastY);}
  function draw(e){if(!drawing)return;e.preventDefault();const pos=getPos(e);ctx.beginPath();ctx.moveTo(lastX,lastY);ctx.lineTo(pos.x,pos.y);ctx.stroke();currentStroke.push({x:pos.x,y:pos.y});lastX=pos.x;lastY=pos.y;}
  function stopDraw(){if(!drawing)return;drawing=false;if(currentStroke.length>1)drawnStrokes[which].push([...currentStroke]);currentStroke=[];}
  newCanvas.addEventListener('mousedown',startDraw);
  newCanvas.addEventListener('mousemove',draw);
  newCanvas.addEventListener('mouseup',stopDraw);
  newCanvas.addEventListener('mouseleave',stopDraw);
  newCanvas.addEventListener('touchstart',startDraw,{passive:false});
  newCanvas.addEventListener('touchmove',draw,{passive:false});
  newCanvas.addEventListener('touchend',stopDraw);
}

function clearCanvases(){
  if(!kwriteSelectedLetter)return;
  drawnStrokes.U=[];drawnStrokes.L=[];
  drawRuledLines('canvasU',kwriteColor);
  drawRuledLines('canvasL',kwriteColor);
  setupCanvas('canvasU','U');
  setupCanvas('canvasL','L');
  hideKWriteEmojiPop();
  hideKWriteActionBtns();
}

function countInkPixels(canvasId){
  const canvas=document.getElementById(canvasId);
  if(!canvas)return 0;
  const ctx=canvas.getContext('2d');
  const{data}=ctx.getImageData(0,0,CANVAS_W,CANVAS_H);
  let count=0;
  for(let i=0;i<data.length;i+=4){
    const a=data[i+3];if(a<80)continue;
    const r=data[i],g=data[i+1],b=data[i+2];
    const rr=Math.round(r*(a/255)+255*(1-a/255));
    const rg=Math.round(g*(a/255)+255*(1-a/255));
    const rb=Math.round(b*(a/255)+255*(1-a/255));
    if(Math.abs(rr-BG_R)>BG_TOL||Math.abs(rg-BG_G)>BG_TOL||Math.abs(rb-BG_B)>BG_TOL)count++;
  }
  return count;
}

/*
  LENIENT DIRECTION HELPERS
  We only check gross direction — kids' strokes are wobbly.
*/
function strokeDirection(pts){
  if(pts.length < 2) return 'none';

  const dx = pts[pts.length - 1].x - pts[0].x;
  const dy = pts[pts.length - 1].y - pts[0].y;

  const adx = Math.abs(dx), ady = Math.abs(dy);

  if(ady > adx * 1.5) return dy > 0 ? 'down' : 'up';
  if(adx > ady * 1.5) return dx > 0 ? 'right' : 'left';

  if(dx > 0 && dy > 0) return 'diag-down-right';
  if(dx < 0 && dy > 0) return 'diag-down-left';
  if(dx > 0 && dy < 0) return 'diag-up-right';

  return 'diag-up-left';
}

function isCurved(pts){
  if(pts.length < 4) return false;

  const start = pts[0];
  const end = pts[pts.length - 1];
  const mid = pts[Math.floor(pts.length / 2)];

  return Math.hypot(
    mid.x - (start.x + end.x) / 2,
    mid.y - (start.y + end.y) / 2
  ) > 12;
}

function getSimpleDirection(dir){
  if(dir === 'up' || dir === 'down') return 'vertical';
  if(dir === 'left' || dir === 'right') return 'horizontal';
  if(dir.includes('diag')) return 'diagonal';
  return 'none';
}

/*
  LENIENT LETTER CHECKS for kindergarten kids.

  Rules:
  - min strokes lowered (most letters now accept 1 stroke minimum)
  - max strokes raised generously (+3 extra allowed) so kids who
    lift the pen mid-letter aren't penalized
  - check() simplified: we only look for the MOST obvious feature
    of the letter (e.g. "any vertical stroke" or "any curved mark")
  - size check removed — even small scribbles are accepted
  - direction check is approximate (uses 1.5 ratio not 1.8)
*/
const LETTER_CHECKS = {
  A: {
    min: 2,
    check: s =>
      s.some(p => getSimpleDirection(strokeDirection(p)) === 'vertical') &&
      s.some(p => getSimpleDirection(strokeDirection(p)) === 'diagonal')
  },

  B: {
    min: 2,
    check: s =>
      s.some(p => isCurved(p)) &&
      s.some(p => getSimpleDirection(strokeDirection(p)) === 'vertical')
  },

  C: { min: 1, check: s => s.some(p => isCurved(p)) },

  D: {
    min: 2,
    check: s =>
      s.some(p => isCurved(p)) &&
      s.some(p => getSimpleDirection(strokeDirection(p)) === 'vertical')
  },

  E: {
    min: 2,
    check: s =>
      s.some(p => getSimpleDirection(strokeDirection(p)) === 'horizontal') &&
      s.some(p => getSimpleDirection(strokeDirection(p)) === 'vertical')
  },

  F: {
    min: 2,
    check: s =>
      s.some(p => getSimpleDirection(strokeDirection(p)) === 'horizontal') &&
      s.some(p => getSimpleDirection(strokeDirection(p)) === 'vertical')
  },

  G: { min: 1, check: s => s.some(p => isCurved(p)) },

  H: {
    min: 3,
    check: s =>
      s.some(p => getSimpleDirection(strokeDirection(p)) === 'vertical')
  },

  I: {
    min: 1,
    check: s =>
      s.some(p => getSimpleDirection(strokeDirection(p)) === 'vertical')
  },

  J: { min: 1, check: s => s.some(p => isCurved(p)) },

  K: {
    min: 3,
    check: s =>
      s.some(p => getSimpleDirection(strokeDirection(p)) === 'vertical') &&
      s.some(p => getSimpleDirection(strokeDirection(p)) === 'diagonal')
  },

  L: {
    min: 2,
    check: s =>
      s.some(p => getSimpleDirection(strokeDirection(p)) === 'vertical') &&
      s.some(p => getSimpleDirection(strokeDirection(p)) === 'horizontal')
  },

  M: {
    min: 3,
    check: s =>
      s.length >= 3 &&
      s.some(p => getSimpleDirection(strokeDirection(p)) === 'vertical')
  },

  N: {
    min: 3,
    check: s =>
      s.some(p => getSimpleDirection(strokeDirection(p)) === 'diagonal')
  },

  O: { min: 1, check: s => s.some(p => isCurved(p)) },

  P: {
    min: 2,
    check: s =>
      s.some(p => isCurved(p)) &&
      s.some(p => getSimpleDirection(strokeDirection(p)) === 'vertical')
  },

  Q: {
    min: 2,
    check: s =>
      s.some(p => isCurved(p)) &&
      s.length >= 2
  },

  R: {
    min: 3,
    check: s =>
      s.some(p => getSimpleDirection(strokeDirection(p)) === 'vertical') &&
      s.some(p => isCurved(p))
  },

  S: { min: 1, check: s => s.some(p => isCurved(p)) },

  T: {
    min: 2,
    check: s =>
      s.some(p => getSimpleDirection(strokeDirection(p)) === 'vertical') &&
      s.some(p => getSimpleDirection(strokeDirection(p)) === 'horizontal')
  },

  U: { min: 1, check: s => s.some(p => isCurved(p)) },

  V: {
    min: 2,
    check: s =>
      s.some(p => getSimpleDirection(strokeDirection(p)) === 'diagonal')
  },

  W: {
    min: 3,
    check: s =>
      s.length >= 3 &&
      s.some(p => getSimpleDirection(strokeDirection(p)) === 'diagonal')
  },

  X: {
    min: 2,
    check: s =>
      s.some(p => getSimpleDirection(strokeDirection(p)) === 'diagonal')
  },

  Y: {
    min: 2,
    check: s =>
      s.some(p => getSimpleDirection(strokeDirection(p)) === 'diagonal') &&
      s.some(p => getSimpleDirection(strokeDirection(p)) === 'vertical')
  },

  Z: {
    min: 3,
    check: s =>
      s.some(p => getSimpleDirection(strokeDirection(p)) === 'horizontal') &&
      s.some(p => getSimpleDirection(strokeDirection(p)) === 'vertical')
  },

  Ng: {
    min: 3,
    check: s => s.length >= 2
  }
};

function scoreBasic(strokes){
  let score = 0;

  if(strokes.length >= 1) score++;

  // check any directional presence
  if(strokes.some(p => strokeDirection(p) !== 'none')) score++;

  return score;
}

function scoreCurve(strokes){
  let score = 0;

  if(strokes.length >= 1) score++;

  if(strokes.some(p => isCurved(p))) score++;

  return score;
}
/*
  LENIENT evaluateDrawing:
  - Only fails if canvas is completely empty (no ink at all) or
    if no strokes were recorded at all.
  - Any actual drawing attempt is considered a pass — ang importante,
    sinubukan ng bata at nakita niya ang hugis ng letra!
*/
function evaluateDrawing(which){
  const key = lookupKey(kwriteSelectedLetter);
  const rule = LETTER_CHECKS[key];
  const strokes = drawnStrokes[which];
  const ink = countInkPixels(which === 'U' ? 'canvasU' : 'canvasL');

  if(ink < MIN_INK || strokes.length === 0){
    return { pass:false, reason:'no_input' };
  }

  if(!rule){
    return { pass:false, reason:'unknown_letter' };
  }

  // STRICT
  if(rule.check(strokes)){
    return { pass:true, reason:'correct' };
  }

  // TRAITS DETECTION
  let traitsMatched = {
    vertical: false,
    horizontal: false,
    diagonal: false,
    curved: false
  };

  strokes.forEach(p => {
    const dir = getSimpleDirection(strokeDirection(p));

    if(dir === 'vertical') traitsMatched.vertical = true;
    if(dir === 'horizontal') traitsMatched.horizontal = true;
    if(dir === 'diagonal') traitsMatched.diagonal = true;
    if(isCurved(p)) traitsMatched.curved = true;
  });

  let matchCount = 0;

  if(traitsMatched.vertical) matchCount++;
  if(traitsMatched.horizontal) matchCount++;
  if(traitsMatched.diagonal) matchCount++;
  if(traitsMatched.curved) matchCount++;

  // ✅ TIGHTER LENIENCY (IMPORTANT FIX)
  if(matchCount >= 2 && strokes.length >= 2){
    return { pass:true, reason:'almost' };
  }

  return { pass:false, reason:'wrong_letter' };
}

function checkWriting(){
  if(!kwriteSelectedLetter)return;

  const resU = evaluateDrawing('U');
  const resL = evaluateDrawing('L');

  // Handle U
  if(!resU.pass){
    sfxWrong();
    screenFlash('wrong');
    flashCanvas('canvasU');
    showKWriteEmojiPop('❌');
    showKWriteActionBtns(false);
    return;
  }

  // Handle L
  if(!resL.pass){
    sfxWrong();
    screenFlash('wrong');
    flashCanvas('canvasL');
    showKWriteEmojiPop('❌');
    showKWriteActionBtns(false);
    return;
  }

  // BOTH PASSED (including "almost")
  if(resU.reason === 'almost' || resL.reason === 'almost'){
    // Almost correct feedback
    sfxFanfare();
    screenFlash('correct');
    showKWriteEmojiPop('👍');

    // STILL allow next (important)
    showKWriteActionBtns(true);
    return;
  }

  // Perfect
  sfxFanfare();
  screenFlash('correct');
  launchConfettiBig(kwriteColor);
  showKWriteEmojiPop('🎉');
  showKWriteActionBtns(true);
}

function flashCanvas(canvasId){
  const canvas=document.getElementById(canvasId);
  if(!canvas)return;
  canvas.classList.remove('error-flash');
  void canvas.offsetWidth;
  canvas.classList.add('error-flash');
  canvas.addEventListener('animationend',()=>canvas.classList.remove('error-flash'),{once:true});
}

function showKWriteEmojiPop(emoji){
  const el=document.getElementById('kwriteEmojiPop');
  el.textContent=emoji;
  el.classList.add('show');
}
function hideKWriteEmojiPop(){
  const el=document.getElementById('kwriteEmojiPop');
  el.classList.remove('show');
  el.textContent='';
}

function showKWriteActionBtns(showNext){
  const wrap=document.getElementById('kwriteActionBtns');
  wrap.style.display='flex';
  const nextBtn=document.getElementById('kwriteNextBtn');
  nextBtn.style.opacity=showNext?'1':'0';
  nextBtn.style.pointerEvents=showNext?'auto':'none';
  document.getElementById('kwriteNextBtn').style.background=kwriteColor;
}
function hideKWriteActionBtns(){
  document.getElementById('kwriteActionBtns').style.display='none';
}

/* ============================================================
   FEEDBACK HELPERS
   ============================================================ */
function showNextBtn(id,bg){const el=document.getElementById(id);if(!el)return;el.style.background=bg;el.classList.add('show');}
function hideNextBtn(id){const el=document.getElementById(id);if(el)el.classList.remove('show');}

function getTagalogPhonetic(word){
  if(!word) return word;

  // Basic phonetic improvements for Tagalog
  return word
    .replace(/ph/gi, 'f')
    .replace(/th/gi, 't')
    .replace(/c(?=[eiy])/gi, 's')
    .replace(/c/gi, 'k')
    .replace(/x/gi, 'ks')
    .replace(/qu/gi, 'k')
    .replace(/ng/gi, 'nang')
    .replace(/ñ/gi, 'ny');
}

/* ============================================================
   INIT
   ============================================================ */
document.addEventListener('keydown',e=>{if(e.key==='Escape')closeM();});
applyLangUI();
renderGrid('en');
loadRandomKWriteLetter();
initSongSection();
newMatchQ();
newMissingQ();
</script>
<!-- ── TAKE QUIZ BUTTON ── -->
<style>
.quiz-btn-wrap {
    display: flex;
    justify-content: center;
    padding: 32px 16px 56px;
}
.take-quiz-btn {
    display: inline-flex;
    align-items: center;
    gap: 12px;
    background: linear-gradient(135deg, #0d5407 0%, #1a8c0a 100%);
    color: #fff;
    font-family: 'Fredoka One', cursive;
    font-size: 1.35rem;
    padding: 18px 48px;
    border-radius: 60px;
    text-decoration: none;
    box-shadow: 0 8px 28px rgba(13,84,7,0.35);
    transition: transform 0.25s cubic-bezier(.34,1.56,.64,1), box-shadow 0.25s;
    letter-spacing: 0.5px;
    border: 3px solid rgba(255,255,255,0.2);
}
.take-quiz-btn:hover {
    transform: translateY(-5px) scale(1.05);
    box-shadow: 0 16px 40px rgba(13,84,7,0.4);
    color: #fff;
}
.take-quiz-btn:active { transform: scale(0.97); }
.take-quiz-btn .quiz-icon { font-size: 1.5rem; }
</style>

<div class="quiz-btn-wrap">
    <a href="activities.php" class="take-quiz-btn">
        Take Quiz
    </a>
</div>

</body>
</html>