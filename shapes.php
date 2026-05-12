<?php
/* ══ LEARN DATA ══ */
$shapes = [
    "Circle"    => ["#FF6B6B","M50,10 A40,40 0 1,1 49.99,10 Z"],
    "Square"    => ["#4ECDC4","M10,10 H90 V90 H10 Z"],
    "Triangle"  => ["#FFE66D","M50,5 L95,90 L5,90 Z"],
    "Rectangle" => ["#A29BFE","M5,20 H95 V80 H5 Z"],
    "Oval"      => ["#FD79A8","M50,20 A40,30 0 1,1 49.99,20 Z"],
    "Star"      => ["#FDCB6E","M50,5 L61,35 L95,35 L68,57 L79,91 L50,70 L21,91 L32,57 L5,35 L39,35 Z"],
    "Heart"     => ["#E17055","M50,80 C50,80 10,55 10,30 A20,20 0 0,1 50,25 A20,20 0 0,1 90,30 C90,55 50,80 50,80 Z"],
    "Diamond"   => ["#00CEC9","M50,5 L95,50 L50,95 L5,50 Z"],
];

$shapes_en = [
    ["Circle",    "Round, no corners!"],
    ["Square",    "4 equal sides!"],
    ["Triangle",  "3 sides, 3 corners!"],
    ["Rectangle", "Like a square, but longer!"],
    ["Oval",      "A stretched circle!"],
    ["Star",      "5 pointy tips!"],
    ["Heart",     "The love shape!"],
    ["Diamond",   "4 sides like a gem!"],
];
$shapes_tl = [
    ["Bilog",     "Pabilog, walang sulok!"],
    ["Parisukat", "4 pantay na gilid!"],
    ["Tatsulok",  "3 gilid, 3 sulok!"],
    ["Parihaba",  "Parang parisukat, mas mahaba!"],
    ["Itlog",     "Parang bilog, mas haba!"],
    ["Bituin",    "5 matulis na dulo!"],
    ["Puso",      "Hugis ng pagmamahal!"],
    ["Dyamante",  "4 gilid, tulad ng hiyas!"],
];

/*
 * ══ REAL LIFE DATA ══
 * img: local path inside pictures/ folder
 * Format: [enName, enFact, tlName, tlFact, svgFallback, imgPath]
 */
$reallife = [
  /* ── CIRCLE ── */
  "#FF6B6B" => ["M50,10 A40,40 0 1,1 49.99,10 Z", [
    ["Moon",  "The moon is a big circle!",   "Buwan",  "Bilog ang buwan!",
      '<circle cx="50" cy="50" r="38" fill="#FFF176" stroke="#F9A825" stroke-width="2"/>
       <circle cx="34" cy="36" r="5" fill="#F9A825" opacity=".35"/>
       <circle cx="58" cy="28" r="3.5" fill="#F9A825" opacity=".35"/>',
      'pictures/moon.png'],
    ["Clock", "A clock face is a circle!",   "Orasan", "Bilog ang mukha ng orasan!",
      '<circle cx="50" cy="50" r="38" fill="#fff" stroke="#546E7A" stroke-width="5"/>
       <line x1="50" y1="18" x2="50" y2="50" stroke="#263238" stroke-width="4" stroke-linecap="round"/>
       <line x1="50" y1="50" x2="68" y2="60" stroke="#263238" stroke-width="3" stroke-linecap="round"/>
       <circle cx="50" cy="50" r="4" fill="#E53935"/>',
      'pictures/clock.jpg'],
    ["Donut", "A donut has a circle shape!", "Donut",  "Bilog ang donut!",
      '<circle cx="50" cy="50" r="36" fill="#FF8A65" stroke="#E64A19" stroke-width="3"/>
       <circle cx="50" cy="50" r="17" fill="#fff9f0"/>',
      'pictures/donut.jpg'],
  ]],

  /* ── SQUARE ── */
  "#4ECDC4" => ["M10,10 H90 V90 H10 Z", [
    ["Window",     "Windows are square!",             "Bintana",   "Parisukat ang bintana!",
      '<rect x="12" y="12" width="76" height="76" rx="3" fill="#B3E5FC" stroke="#546E7A" stroke-width="4"/>
       <line x1="50" y1="12" x2="50" y2="88" stroke="#546E7A" stroke-width="3"/>
       <line x1="12" y1="50" x2="88" y2="50" stroke="#546E7A" stroke-width="3"/>',
      'pictures/window.jpg'],
    ["Waffle",     "A waffle is a square!",           "Waffle",    "Parisukat ang waffle!",
      '<rect x="10" y="10" width="80" height="80" rx="8" fill="#FFCC80" stroke="#E65100" stroke-width="3"/>
       <line x1="10" y1="37" x2="90" y2="37" stroke="#E65100" stroke-width="2.5"/>
       <line x1="10" y1="63" x2="90" y2="63" stroke="#E65100" stroke-width="2.5"/>
       <line x1="37" y1="10" x2="37" y2="90" stroke="#E65100" stroke-width="2.5"/>
       <line x1="63" y1="10" x2="63" y2="90" stroke="#E65100" stroke-width="2.5"/>',
      'pictures/waffle.jpg'],
    ["Floor Tile", "Tiles on the floor are squares!", "Tiles",     "Parisukat ang tiles sa sahig!",
      '<rect x="8" y="8" width="38" height="38" rx="3" fill="#B2EBF2" stroke="#80DEEA" stroke-width="2"/>
       <rect x="54" y="8" width="38" height="38" rx="3" fill="#E0F7FA" stroke="#80DEEA" stroke-width="2"/>
       <rect x="8" y="54" width="38" height="38" rx="3" fill="#E0F7FA" stroke="#80DEEA" stroke-width="2"/>
       <rect x="54" y="54" width="38" height="38" rx="3" fill="#B2EBF2" stroke="#80DEEA" stroke-width="2"/>',
      'pictures/floortile.jpg'],
  ]],

  /* ── TRIANGLE ── */
  "#FFE66D" => ["M50,5 L95,90 L5,90 Z", [
    ["Pizza Slice", "A pizza slice is a triangle!", "Hiwang Pizza", "Tatsulok ang hiwang pizza!",
      '<polygon points="50,8 92,88 8,88" fill="#FFCC80" stroke="#E65100" stroke-width="3"/>
       <polygon points="50,8 92,88 8,88" fill="#EF5350" opacity=".7"/>
       <circle cx="50" cy="55" r="5" fill="#fff" opacity=".85"/>',
      'pictures/pizzaslice.png'],
    ["Mountain",    "Mountains look like triangles!", "Bundok",       "Parang tatsulok ang bundok!",
      '<polygon points="50,8 90,82 10,82" fill="#78909C" stroke="#37474F" stroke-width="2.5"/>
       <polygon points="50,8 63,36 37,36" fill="#ECEFF1"/>
       <rect x="0" y="82" width="100" height="16" fill="#A5D6A7"/>',
      'pictures/mountain.jpg'],
    ["Xmas Tree",   "A Christmas tree is a triangle!", "Puno ng Pasko", "Tatsulok ang puno ng Pasko!",
      '<polygon points="50,6 86,70 14,70" fill="#2E7D32" stroke="#1B5E20" stroke-width="2"/>
       <rect x="42" y="70" width="16" height="18" fill="#5D4037"/>
       <circle cx="50" cy="16" r="5" fill="#FFD700" stroke="#FFA000" stroke-width="2"/>',
      'pictures/xmastree.jpg'],
  ]],

  /* ── RECTANGLE ── */
  "#A29BFE" => ["M5,20 H95 V80 H5 Z", [
    ["Phone",     "Your phone screen is a rectangle!", "Telepono", "Parihaba ang screen ng telepono!",
      '<rect x="28" y="5" width="44" height="90" rx="8" fill="#37474F" stroke="#263238" stroke-width="3"/>
       <rect x="33" y="15" width="34" height="62" rx="3" fill="#B3E5FC"/>
       <circle cx="50" cy="88" r="4" fill="#546E7A"/>',
      'pictures/phone.jpg'],
    ["Door",      "A door is a tall rectangle!",      "Pinto",    "Parihaba ang pinto!",
      '<rect x="20" y="8" width="60" height="84" rx="5" fill="#FFCC80" stroke="#5D4037" stroke-width="3"/>
       <rect x="25" y="14" width="22" height="30" rx="2" fill="#FFE0B2"/>
       <rect x="53" y="14" width="22" height="30" rx="2" fill="#FFE0B2"/>
       <circle cx="67" cy="52" r="4" fill="#FFA000"/>',
      'pictures/door.jpg'],
    ["Brick",     "Bricks are rectangles!",           "Tisa",     "Parihaba ang tisa!",
      '<rect x="6" y="8" width="40" height="22" rx="3" fill="#EF5350" stroke="#B71C1C" stroke-width="1.5"/>
       <rect x="54" y="8" width="40" height="22" rx="3" fill="#EF9A9A" stroke="#B71C1C" stroke-width="1.5"/>
       <rect x="6" y="37" width="40" height="22" rx="3" fill="#EF9A9A" stroke="#B71C1C" stroke-width="1.5"/>
       <rect x="54" y="37" width="40" height="22" rx="3" fill="#EF5350" stroke="#B71C1C" stroke-width="1.5"/>',
      'pictures/brick.jpg'],
  ]],

  /* ── OVAL ── */
  "#FD79A8" => ["M50,20 A40,30 0 1,1 49.99,20 Z", [
    ["Egg",    "An egg is an oval!",          "Itlog",  "Bilog-haba ang itlog!",
      '<ellipse cx="50" cy="55" rx="30" ry="38" fill="#FFF9C4" stroke="#F9A825" stroke-width="3"/>',
      'pictures/egg.jpg'],
    ["Mango",  "A mango is oval-shaped!",     "Mangga", "Bilog-haba ang mangga!",
      '<ellipse cx="50" cy="56" rx="30" ry="36" fill="#FFCA28" stroke="#F57F17" stroke-width="2.5"/>
       <path d="M50,20 C50,20 44,8 50,4 C56,8 50,20 50,20Z" fill="#66BB6A" stroke="#2E7D32" stroke-width="2"/>',
      'pictures/mango.png'],
    ["Soap", "A soap is oval-shaped!", "Sabon", "Bilog-haba ang sabon!",
  '<ellipse cx="50" cy="50" rx="30" ry="40" fill="#FFCCBC" stroke="#FF8A65" stroke-width="2.5"/>
   <ellipse cx="40" cy="40" rx="8" ry="5" fill="#FFF" opacity=".4"/>',
  'pictures/soap.png'],
  ]],

  /* ── STAR ── */
  "#FDCB6E" => ["M50,5 L61,35 L95,35 L68,57 L79,91 L50,70 L21,91 L32,57 L5,35 L39,35 Z", [
    ["Star",      "Stars in the sky are star-shaped!",  "Bituin",  "Bituin ang hugis ng bituin sa langit!",
      '<polygon points="50,5 61,35 95,35 68,57 79,91 50,70 21,91 32,57 5,35 39,35" fill="#FFD700" stroke="#FFA000" stroke-width="2.5"/>',
      'pictures/star.jpg'],
    ["Starfish",  "A starfish has five arms!",           "Starfish","Limang braso ng starfish!",
      '<polygon points="50,5 61,35 95,35 68,57 79,91 50,70 21,91 32,57 5,35 39,35" fill="#FF8A65" stroke="#E64A19" stroke-width="2.5"/>
       <circle cx="50" cy="50" r="11" fill="#FFAB91"/>',
      'pictures/starfish.jpg'],
    ["Medal",     "A winner\'s medal is star-shaped!",  "Medalya", "Bituin ang hugis ng medalya!",
      '<polygon points="50,26 61,54 93,54 68,72 78,98 50,80 22,98 32,72 7,54 39,54" fill="#FFD700" stroke="#F57F17" stroke-width="2.5"/>
       <circle cx="50" cy="63" r="13" fill="#FFF176" stroke="#F57F17" stroke-width="1.5"/>',
      'pictures/medal.png'],
  ]],

  /* ── HEART ── */
  "#E17055" => ["M50,80 C50,80 10,55 10,30 A20,20 0 0,1 50,25 A20,20 0 0,1 90,30 C90,55 50,80 50,80 Z", [
    ["Heart",       "Hearts mean love!",                    "Puso",       "Simbolo ng pagmamahal ang puso!",
      '<path d="M50,80 C50,80 10,55 10,30 A20,20 0 0,1 50,25 A20,20 0 0,1 90,30 C90,55 50,80 50,80Z" fill="#F44336" stroke="#C62828" stroke-width="3"/>',
      'pictures/heart.png'],
    ["Strawberry",  "A strawberry looks like a heart!",     "Strawberry", "Parang puso ang hugis ng strawberry!",
      '<path d="M50,80 C50,80 10,55 10,30 A20,20 0 0,1 50,25 A20,20 0 0,1 90,30 C90,55 50,80 50,80Z" fill="#F44336" stroke="#C62828" stroke-width="2.5"/>
       <path d="M36,14 C36,14 42,5 50,8 C58,5 64,14 64,14 L57,26 L43,26Z" fill="#66BB6A" stroke="#2E7D32" stroke-width="1.5"/>',
      'pictures/strawnerry.jpg'],
    ["Peach",       "A peach looks like a heart!",          "Peach",      "Parang puso ang hugis ng peach!",
      '<path d="M50,80 C50,80 10,55 10,30 A20,20 0 0,1 50,25 A20,20 0 0,1 90,30 C90,55 50,80 50,80Z" fill="#FFAB91" stroke="#E64A19" stroke-width="2.5"/>
       <path d="M47,26 C47,26 43,15 50,10 C57,15 53,26 53,26" fill="#66BB6A" stroke="#2E7D32" stroke-width="2"/>',
      'pictures/peach.png'],
  ]],

  /* ── DIAMOND ── */
  "#00CEC9" => ["M50,5 L95,50 L50,95 L5,50 Z", [
    ["Gem",       "A diamond gem sparkles!",                "Hiyas",      "Kumikislap ang hiyas na dyamante!",
      '<polygon points="50,5 95,50 50,95 5,50" fill="#B2EBF2" stroke="#006064" stroke-width="2.5"/>
       <polygon points="50,5 75,38 50,50 25,38" fill="#E0F7FA"/>',
      'pictures/gem.jpg'],
    ["Kite",      "A kite flies in a diamond shape!",       "Saranggola", "Dyamante ang hugis ng saranggola!",
      '<polygon points="50,5 90,50 50,90 10,50" fill="#FF8A80" stroke="#C62828" stroke-width="2.5"/>
       <line x1="50" y1="5" x2="50" y2="90" stroke="#C62828" stroke-width="2"/>
       <line x1="10" y1="50" x2="90" y2="50" stroke="#C62828" stroke-width="2"/>',
      'pictures/kite.jpg'],
    ["Tile", "Some tiles are diamond-shaped!", "Tiles", "May mga tiles na dyamante ang ayos!",
  '<polygon points="50,10 90,50 50,90 10,50" fill="#B2DFDB" stroke="#00695C" stroke-width="2.5"/>',
  'pictures/tile.jpg'],
  ]],
];

$drawShapes = [
    "Circle"    => ["#FF6B6B", "Curve all the way around!", "Ikutin pababa!",
        ["M50,10 A40,40 0 0,1 90,50","M90,50 A40,40 0 0,1 50,90","M50,90 A40,40 0 0,1 10,50","M10,50 A40,40 0 0,1 50,10"]],
    "Square"    => ["#4ECDC4", "4 equal straight lines!", "4 pantay na linya!",
        ["M10,10 H90","M90,10 V90","M90,90 H10","M10,90 V10"]],
    "Triangle"  => ["#FFE66D", "3 lines, meet at the top!", "3 linya, magtagpo sa itaas!",
        ["M50,5 L95,90","M95,90 L5,90","M5,90 L50,5"]],
    "Rectangle" => ["#A29BFE", "Like a square but wider!", "Parang parisukat, mas malawak!",
        ["M5,20 H95","M95,20 V80","M95,80 H5","M5,80 V20"]],
    "Oval"      => ["#FD79A8", "A stretched-out circle!", "Bilog na nakaunat!",
        ["M50,20 A40,30 0 0,1 90,50","M90,50 A40,30 0 0,1 50,80","M50,80 A40,30 0 0,1 10,50","M10,50 A40,30 0 0,1 50,20"]],
    "Star"      => ["#FDCB6E", "5 zigzag points!", "5 matulis na dulo!",
        ["M50,5 L61,35 L39,35 Z","M61,35 L95,35 L68,57","M68,57 L79,91 L50,70","M50,70 L21,91 L32,57","M32,57 L5,35 L39,35"]],
    "Heart"     => ["#E17055", "Two bumps, point at bottom!", "Dalawang kurbada, tumuro sa ibaba!",
        ["M50,30 A20,20 0 0,1 90,30","M90,30 C90,55 50,80 50,80","M50,80 C50,80 10,55 10,30","M10,30 A20,20 0 0,1 50,30"]],
    "Diamond"   => ["#00CEC9", "A square turned sideways!", "Parisukat na nakagilid!",
        ["M50,5 L95,50","M95,50 L50,95","M50,95 L5,50","M5,50 L50,5"]],
];

$shapeKeys  = array_keys($shapes);
$drawKeys   = array_keys($drawShapes);
$shapePaths = array_values(array_column(array_values($shapes), 1));

$quizItems = [
    ['<circle cx="50" cy="50" r="36" fill="#FF8A65" stroke="#E64A19" stroke-width="3"/><circle cx="50" cy="22" r="4" fill="#F48FB1"/><circle cx="74" cy="36" r="3.5" fill="#80DEEA"/><circle cx="72" cy="66" r="4" fill="#FFF176"/><circle cx="26" cy="62" r="3.5" fill="#CE93D8"/><circle cx="50" cy="50" r="17" fill="#fff9f0"/>',
      'Donut', 'Donut', 0],
    ['<rect x="10" y="10" width="80" height="80" rx="8" fill="#FFCC80" stroke="#E65100" stroke-width="3"/><line x1="10" y1="30" x2="90" y2="30" stroke="#E65100" stroke-width="2.5"/><line x1="10" y1="50" x2="90" y2="50" stroke="#E65100" stroke-width="2.5"/><line x1="10" y1="70" x2="90" y2="70" stroke="#E65100" stroke-width="2.5"/><line x1="30" y1="10" x2="30" y2="90" stroke="#E65100" stroke-width="2.5"/><line x1="50" y1="10" x2="50" y2="90" stroke="#E65100" stroke-width="2.5"/><line x1="70" y1="10" x2="70" y2="90" stroke="#E65100" stroke-width="2.5"/>',
      'Waffle', 'Waffle', 1],
    ['<polygon points="50,8 92,88 8,88" fill="#FFCC80" stroke="#E65100" stroke-width="3"/><polygon points="50,8 92,88 8,88" fill="#EF5350" opacity=".7"/><circle cx="50" cy="55" r="5" fill="#fff" opacity=".85"/><circle cx="37" cy="70" r="4" fill="#fff" opacity=".85"/><circle cx="64" cy="67" r="4.5" fill="#fff" opacity=".85"/>',
      'Pizza Slice', 'Hiwang Pizza', 2],
    ['<rect x="28" y="5" width="44" height="90" rx="8" fill="#37474F" stroke="#263238" stroke-width="3"/><rect x="33" y="15" width="34" height="62" rx="3" fill="#B3E5FC"/><circle cx="50" cy="88" r="4" fill="#546E7A"/><rect x="42" y="8" width="16" height="3" rx="2" fill="#546E7A"/>',
      'Phone', 'Telepono', 3],
    ['<ellipse cx="50" cy="56" rx="30" ry="36" fill="#FFCA28" stroke="#F57F17" stroke-width="2.5"/><ellipse cx="38" cy="38" rx="8" ry="5" fill="#FFE082" opacity=".6" transform="rotate(-30 38 38)"/><path d="M50,20 C50,20 44,8 50,4 C56,8 50,20 50,20Z" fill="#66BB6A" stroke="#2E7D32" stroke-width="2"/>',
      'Mango', 'Mangga', 4],
    ['<polygon points="50,5 61,35 95,35 68,57 79,91 50,70 21,91 32,57 5,35 39,35" fill="#FF8A65" stroke="#E64A19" stroke-width="2.5"/><circle cx="50" cy="50" r="11" fill="#FFAB91"/><circle cx="50" cy="50" r="6" fill="#FF7043"/>',
      'Starfish', 'Starfish', 5],
    ['<path d="M50,80 C50,80 10,55 10,30 A20,20 0 0,1 50,25 A20,20 0 0,1 90,30 C90,55 50,80 50,80Z" fill="#F44336" stroke="#C62828" stroke-width="2.5"/><path d="M36,14 C36,14 42,5 50,8 C58,5 64,14 64,14 L57,26 L43,26Z" fill="#66BB6A" stroke="#2E7D32" stroke-width="1.5"/><circle cx="37" cy="43" r="2.5" fill="#fff" opacity=".5"/><circle cx="52" cy="36" r="2" fill="#fff" opacity=".5"/>',
      'Strawberry', 'Strawberry', 6],
    ['<polygon points="50,5 90,50 50,90 10,50" fill="#FF8A80" stroke="#C62828" stroke-width="2.5"/><line x1="50" y1="5" x2="50" y2="90" stroke="#C62828" stroke-width="2"/><line x1="10" y1="50" x2="90" y2="50" stroke="#C62828" stroke-width="2"/>',
      'Kite', 'Saranggola', 7],
];

/* ══ FIND THE SHAPE — PLAYGROUND SCENE DATA ══ */
$findRoundsData = [
  ['name'=>'Circle','nameTL'=>'Bilog','color'=>'#FF6B6B','path'=>'M50,10 A40,40 0 1,1 49.99,10 Z','findCount'=>3,'objects'=>[
    ['Ball','Bola','<circle cx="50" cy="50" r="38" fill="#EF5350" stroke="#B71C1C" stroke-width="2"/><ellipse cx="38" cy="36" rx="9" ry="5" fill="#fff" opacity=".3" transform="rotate(-30 38 36)"/>',true,8,15,72],
    ['Clock','Orasan','<circle cx="50" cy="50" r="38" fill="#fff" stroke="#546E7A" stroke-width="4"/><line x1="50" y1="18" x2="50" y2="50" stroke="#263238" stroke-width="4" stroke-linecap="round"/><line x1="50" y1="50" x2="68" y2="60" stroke="#263238" stroke-width="3" stroke-linecap="round"/><circle cx="50" cy="50" r="4" fill="#E53935"/>',true,53,10,68],
    ['Donut','Donut','<circle cx="50" cy="50" r="36" fill="#FF8A65" stroke="#E64A19" stroke-width="3"/><circle cx="50" cy="22" r="4" fill="#F48FB1"/><circle cx="74" cy="36" r="3.5" fill="#80DEEA"/><circle cx="50" cy="50" r="17" fill="#fff9f0"/>',true,77,22,65],
    ['Door','Pinto','<rect x="20" y="8" width="60" height="84" rx="5" fill="#FFCC80" stroke="#5D4037" stroke-width="3"/><rect x="25" y="14" width="22" height="30" rx="2" fill="#FFE0B2"/><rect x="53" y="14" width="22" height="30" rx="2" fill="#FFE0B2"/><circle cx="67" cy="52" r="4" fill="#FFA000"/>',false,28,42,60],
    ['Star','Bituin','<polygon points="50,5 61,35 95,35 68,57 79,91 50,70 21,91 32,57 5,35 39,35" fill="#FFD700" stroke="#FFA000" stroke-width="2"/>',false,63,45,62],
    ['Kite','Saranggola','<polygon points="50,5 90,50 50,90 10,50" fill="#FF8A80" stroke="#C62828" stroke-width="2.5"/><line x1="50" y1="5" x2="50" y2="90" stroke="#C62828" stroke-width="2"/><line x1="10" y1="50" x2="90" y2="50" stroke="#C62828" stroke-width="2"/>',false,83,8,56],
  ]],
  ['name'=>'Triangle','nameTL'=>'Tatsulok','color'=>'#FFE66D','path'=>'M50,5 L95,90 L5,90 Z','findCount'=>3,'objects'=>[
    ['Pizza Slice','Hiwang Pizza','<polygon points="50,8 92,88 8,88" fill="#FFCC80" stroke="#E65100" stroke-width="3"/><polygon points="50,8 92,88 8,88" fill="#EF5350" opacity=".65"/><circle cx="50" cy="55" r="5" fill="#fff" opacity=".85"/>',true,7,12,72],
    ['Mountain','Bundok','<polygon points="50,8 90,82 10,82" fill="#78909C" stroke="#37474F" stroke-width="2.5"/><polygon points="50,8 63,36 37,36" fill="#ECEFF1"/><rect x="0" y="82" width="100" height="16" fill="#A5D6A7"/>',true,52,8,75],
    ['Xmas Tree','Puno ng Pasko','<polygon points="50,6 86,70 14,70" fill="#2E7D32" stroke="#1B5E20" stroke-width="2"/><rect x="42" y="70" width="16" height="18" fill="#5D4037"/><circle cx="50" cy="16" r="5" fill="#FFD700"/>',true,78,16,66],
    ['Ball','Bola','<circle cx="50" cy="50" r="38" fill="#2196F3" stroke="#1565C0" stroke-width="2"/>',false,20,48,62],
    ['Phone','Telepono','<rect x="28" y="5" width="44" height="90" rx="8" fill="#37474F" stroke="#263238" stroke-width="3"/><rect x="33" y="15" width="34" height="62" rx="3" fill="#B3E5FC"/><circle cx="50" cy="88" r="4" fill="#546E7A"/>',false,62,50,58],
    ['Heart','Puso','<path d="M50,80 C50,80 10,55 10,30 A20,20 0 0,1 50,25 A20,20 0 0,1 90,30 C90,55 50,80 50,80Z" fill="#F44336" stroke="#C62828" stroke-width="2.5"/>',false,84,42,62],
  ]],
  ['name'=>'Star','nameTL'=>'Bituin','color'=>'#FDCB6E','path'=>'M50,5 L61,35 L95,35 L68,57 L79,91 L50,70 L21,91 L32,57 L5,35 L39,35 Z','findCount'=>3,'objects'=>[
    ['Star','Bituin','<polygon points="50,5 61,35 95,35 68,57 79,91 50,70 21,91 32,57 5,35 39,35" fill="#FFD700" stroke="#FFA000" stroke-width="2.5"/>',true,6,10,70],
    ['Starfish','Starfish','<polygon points="50,5 61,35 95,35 68,57 79,91 50,70 21,91 32,57 5,35 39,35" fill="#FF8A65" stroke="#E64A19" stroke-width="2.5"/><circle cx="50" cy="50" r="11" fill="#FFAB91"/>',true,52,18,70],
    ['Medal','Medalya','<polygon points="50,26 61,54 93,54 68,72 78,98 50,80 22,98 32,72 7,54 39,54" fill="#FFD700" stroke="#F57F17" stroke-width="2.5"/><circle cx="50" cy="63" r="13" fill="#FFF176" stroke="#F57F17" stroke-width="1.5"/>',true,80,14,68],
    ['Mango','Mangga','<ellipse cx="50" cy="56" rx="30" ry="36" fill="#FFCA28" stroke="#F57F17" stroke-width="2.5"/><path d="M50,20 C50,20 44,8 50,4 C56,8 50,20 50,20Z" fill="#66BB6A" stroke="#2E7D32" stroke-width="2"/>',false,18,45,64],
    ['Circle Ball','Bola','<circle cx="50" cy="50" r="38" fill="#EF5350" stroke="#B71C1C" stroke-width="2"/>',false,60,48,62],
    ['Heart','Puso','<path d="M50,80 C50,80 10,55 10,30 A20,20 0 0,1 50,25 A20,20 0 0,1 90,30 C90,55 50,80 50,80Z" fill="#F44336" stroke="#C62828" stroke-width="2.5"/>',false,83,46,62],
  ]],
  ['name'=>'Heart','nameTL'=>'Puso','color'=>'#E17055','path'=>'M50,80 C50,80 10,55 10,30 A20,20 0 0,1 50,25 A20,20 0 0,1 90,30 C90,55 50,80 50,80 Z','findCount'=>3,'objects'=>[
    ['Strawberry','Strawberry','<path d="M50,80 C50,80 10,55 10,30 A20,20 0 0,1 50,25 A20,20 0 0,1 90,30 C90,55 50,80 50,80Z" fill="#F44336" stroke="#C62828" stroke-width="2.5"/><path d="M36,14 C36,14 42,5 50,8 C58,5 64,14 64,14 L57,26 L43,26Z" fill="#66BB6A" stroke="#2E7D32" stroke-width="1.5"/>',true,9,14,72],
    ['Heart Candy','Candy Heart','<path d="M50,80 C50,80 10,55 10,30 A20,20 0 0,1 50,25 A20,20 0 0,1 90,30 C90,55 50,80 50,80Z" fill="#F48FB1" stroke="#E91E63" stroke-width="2.5"/><text x="50" y="57" text-anchor="middle" font-size="10" font-weight="bold" fill="#880E4F" font-family="sans-serif">LOVE</text>',true,55,16,70],
    ['Peach','Peach','<path d="M50,80 C50,80 10,55 10,30 A20,20 0 0,1 50,25 A20,20 0 0,1 90,30 C90,55 50,80 50,80Z" fill="#FFAB91" stroke="#E64A19" stroke-width="2.5"/><path d="M47,26 C47,26 43,15 50,10 C57,15 53,26 53,26" fill="#66BB6A" stroke="#2E7D32" stroke-width="2"/>',true,79,12,68],
    ['Star','Bituin','<polygon points="50,5 61,35 95,35 68,57 79,91 50,70 21,91 32,57 5,35 39,35" fill="#FFD700" stroke="#FFA000" stroke-width="2.5"/>',false,25,48,64],
    ['Waffle','Waffle','<rect x="10" y="10" width="80" height="80" rx="8" fill="#FFCC80" stroke="#E65100" stroke-width="3"/><line x1="10" y1="30" x2="90" y2="30" stroke="#E65100" stroke-width="2"/><line x1="10" y1="50" x2="90" y2="50" stroke="#E65100" stroke-width="2"/><line x1="10" y1="70" x2="90" y2="70" stroke="#E65100" stroke-width="2"/><line x1="30" y1="10" x2="30" y2="90" stroke="#E65100" stroke-width="2"/><line x1="50" y1="10" x2="50" y2="90" stroke="#E65100" stroke-width="2"/><line x1="70" y1="10" x2="70" y2="90" stroke="#E65100" stroke-width="2"/>',false,62,48,62],
    ['Diamond','Dyamante','<polygon points="50,5 95,50 50,95 5,50" fill="#B2EBF2" stroke="#006064" stroke-width="2.5"/>',false,83,44,62],
  ]],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Shapes - E-KINDER</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Fredoka+One&family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root { --green-dark:#0d5407; --bg:#fff9f0; }
        *{ box-sizing:border-box; margin:0; padding:0; }
        body{
            font-family:'Nunito',sans-serif; background:var(--bg);
            background-image:radial-gradient(circle at 15% 20%,rgba(78,205,196,.1) 0%,transparent 40%),
                             radial-gradient(circle at 85% 70%,rgba(255,107,107,.1) 0%,transparent 40%);
            min-height:100vh; overflow-x:hidden;
        }

        /* ══ NAV ══ */
        .lesson-nav{padding:0 28px;height:64px;background:#fff;box-shadow:0 4px 20px rgba(0,0,0,.07);position:sticky;top:0;z-index:200;display:flex;align-items:center;justify-content:space-between;gap:16px;}
        .lnav-back{display:inline-flex;align-items:center;gap:7px;background:var(--green-dark);color:#fff;font-family:'Fredoka One',cursive;font-size:.9rem;padding:8px 18px;border-radius:50px;text-decoration:none;box-shadow:0 4px 12px rgba(13,84,7,.25);transition:transform .2s;flex-shrink:0;}
        .lnav-back:hover{transform:scale(1.06);color:#fff;}
        .lang-toggle{display:inline-flex;align-items:center;background:#f0f0f0;border-radius:50px;padding:4px;border:2px solid #e0e0e0;}
        .lang-btn{font-family:'Fredoka One',cursive;font-size:.82rem;padding:6px 16px;border-radius:50px;border:none;cursor:pointer;background:transparent;color:#aaa;transition:background .2s,color .2s,transform .15s;display:flex;align-items:center;gap:5px;}
        .lang-btn.active{background:var(--green-dark);color:#fff;box-shadow:0 3px 10px rgba(13,84,7,.25);transform:scale(1.04);}

        /* ══ PAGE HEADER ══ */
        .page-header{text-align:center;padding:36px 0 8px;}
        .page-header h1{font-family:'Fredoka One',cursive;font-size:clamp(1.8rem,4vw,2.6rem);color:var(--green-dark);margin-bottom:6px;}
        .lang-badge{display:inline-flex;align-items:center;gap:8px;margin-top:14px;padding:8px 22px;border-radius:50px;font-family:'Fredoka One',cursive;font-size:.9rem;background:#fff;border:2px solid #b6d9b6;color:var(--green-dark);box-shadow:0 2px 10px rgba(13,84,7,.07);transition:all .3s;}
        .lang-badge.tl-mode{background:#fff8f0;border-color:#ffc980;color:#b84a00;}

        /* ══ SECTION HEADER ══ */
        .section-header-card{display:flex;align-items:center;gap:16px;background:#fff;border-radius:20px;padding:18px 24px;box-shadow:0 4px 18px rgba(0,0,0,.06);margin:52px 0 28px;}
        .sec-icon-box{width:56px;height:56px;border-radius:16px;display:flex;align-items:center;justify-content:center;flex-shrink:0;color:#fff;box-shadow:0 6px 16px rgba(0,0,0,.12);}
        .sec-icon-box svg{width:30px;height:30px;}
        .sec-icon-box i{font-size:1.4rem;}
        .sec-title-group{flex:1;min-width:0;}
        .sec-title{font-family:'Fredoka One',cursive;font-size:1.5rem;color:#1a1a2e;line-height:1.15;margin:0;}
        .sec-subtitle{font-size:.875rem;color:#aaa;font-weight:700;margin:3px 0 0;}
        .sec-line{flex:1;height:2px;background:linear-gradient(90deg,#e0e0e0 0%,transparent 100%);border-radius:99px;margin-left:8px;min-width:40px;}

        /* ══ NOW LET'S PLAY DIVIDER ══ */
        .lets-play-divider{display:flex;align-items:center;gap:0;margin:56px 0 48px;position:relative;}
        .lets-play-divider::before,.lets-play-divider::after{content:'';flex:1;height:1.5px;background:linear-gradient(90deg,transparent,#d4c9b0 40%,#d4c9b0 60%,transparent);}
        .lets-play-pill{display:inline-flex;align-items:center;gap:9px;background:var(--bg);border:2px solid #ddd4c0;border-radius:50px;padding:9px 22px;font-family:'Fredoka One',cursive;font-size:.95rem;color:#8a7c5c;white-space:nowrap;margin:0 18px;box-shadow:0 2px 10px rgba(0,0,0,.04);letter-spacing:.01em;}
        .lets-play-pill .pill-icon{font-size:1rem;color:#FDCB6E;}

        /* ══ RL SECTION ══ */
        .rl-hdr{display:flex;align-items:center;gap:10px;margin-bottom:13px;}
        .rl-shape-dot{width:14px;height:14px;border-radius:50%;flex-shrink:0;}
        .rl-hdr-name .rl-sname{font-family:'Fredoka One',cursive;font-size:.95rem;color:#2d2d2d;}
        .rl-hdr-name small.rl-stap{display:block;font-family:'Nunito',sans-serif;font-size:.7rem;font-weight:700;color:#ccc;margin-top:1px;}
        .rl-divline{flex:1;height:1.5px;background:linear-gradient(to right,var(--rc),transparent);border-radius:2px;opacity:.5;}

        /* ══ SECTION 1: LEARN ══ */
        .shapes-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:22px;padding:4px 0 0;}
        @media(max-width:768px){.shapes-grid{grid-template-columns:repeat(2,1fr)}}
        .shape-card{background:#fff;border-radius:24px;padding:24px 16px 18px;text-align:center;cursor:pointer;position:relative;overflow:hidden;border:3px solid transparent;box-shadow:0 6px 18px rgba(0,0,0,.07);transition:transform .25s cubic-bezier(.34,1.56,.64,1),box-shadow .25s;animation:cardIn .5s ease both;}
        .shape-card:hover{transform:translateY(-8px) scale(1.03);box-shadow:0 18px 36px rgba(0,0,0,.12);}
        .shape-card:active{transform:scale(.96);}
        .shape-card::before{content:'';position:absolute;top:0;left:0;right:0;height:5px;background:var(--cc);border-radius:24px 24px 0 0;}
        .shape-card::after{content:'';position:absolute;top:-60%;left:-60%;width:60%;height:200%;background:linear-gradient(105deg,transparent 40%,rgba(255,255,255,.5) 50%,transparent 60%);transition:left .5s;pointer-events:none;}
        .shape-card:hover::after{left:130%;}
        .s-svg{width:90px;height:90px;margin:0 auto 12px;display:flex;align-items:center;justify-content:center;position:relative;}
        .s-svg svg{width:100%;height:100%;filter:drop-shadow(0 5px 10px rgba(0,0,0,.15));transition:transform .3s cubic-bezier(.34,1.56,.64,1);}
        .shape-card:hover .s-svg svg{transform:rotate(-8deg) scale(1.1);}
        .s-svg::after{content:'';position:absolute;inset:-8px;border-radius:50%;border:3px dashed var(--cc);opacity:0;transition:opacity .3s;animation:spinRing 4s linear infinite;}
        .shape-card:hover .s-svg::after{opacity:.5;}
        @keyframes spinRing{to{transform:rotate(360deg)}}
        .s-name{font-family:'Fredoka One',cursive;font-size:1.25rem;color:#2d2d2d;margin-bottom:4px;}
        .s-local{font-size:.76rem;color:#bbb;font-weight:700;margin-bottom:3px;}
        .s-desc{font-size:.82rem;color:#aaa;font-weight:600;line-height:1.4;}
        @keyframes cardIn{from{opacity:0;transform:translateY(28px) scale(.9)}to{opacity:1;transform:none}}

        /* ══ LEARN MODAL ══ */
        .overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);backdrop-filter:blur(6px);z-index:999;align-items:center;justify-content:center;}
        .overlay.active{display:flex;}
        .lm-modal{background:#fff;border-radius:30px;padding:40px 32px 28px;text-align:center;max-width:360px;width:90%;position:relative;box-shadow:0 28px 70px rgba(0,0,0,.22);animation:popUp .35s cubic-bezier(.34,1.56,.64,1) both;}
        @keyframes popUp{from{opacity:0;transform:scale(.6) translateY(40px)}to{opacity:1;transform:none}}
        .mx{position:absolute;top:14px;right:16px;width:32px;height:32px;border-radius:50%;border:none;background:#f0f0f0;cursor:pointer;display:flex;align-items:center;justify-content:center;color:#888;transition:background .2s,transform .2s;font-size:.9rem;}
        .mx:hover{background:#ffe0e0;transform:rotate(90deg);}
        .lm-svg{width:130px;height:130px;margin:0 auto 16px;filter:drop-shadow(0 8px 18px rgba(0,0,0,.16));animation:floaty 2.5s ease-in-out infinite;}
        @keyframes floaty{0%,100%{transform:translateY(0) rotate(-3deg)}50%{transform:translateY(-10px) rotate(3deg)}}
        .lm-name{font-family:'Fredoka One',cursive;font-size:2rem;margin-bottom:3px;}
        .lm-local{font-size:.92rem;color:#bbb;font-weight:700;margin-bottom:5px;}
        .lm-desc{font-size:.93rem;color:#777;font-weight:600;line-height:1.5;margin-bottom:13px;}
        .speak-btn{display:inline-flex;align-items:center;gap:7px;background:var(--mc,#4ECDC4);color:#fff;border:none;border-radius:50px;padding:9px 22px;font-family:'Fredoka One',cursive;font-size:.98rem;cursor:pointer;margin-bottom:11px;box-shadow:0 4px 12px rgba(0,0,0,.14);transition:transform .2s;}
        .speak-btn:hover{transform:scale(1.07);}
        .speak-btn.speaking{animation:speakP .6s ease infinite alternate;}
        @keyframes speakP{from{box-shadow:0 4px 12px rgba(0,0,0,.14)}to{box-shadow:0 4px 24px var(--mc,#4ECDC4)}}
        .btn-close-soft{background:none;color:#ccc;border:2px solid #eee;border-radius:50px;padding:6px 22px;font-family:'Nunito',sans-serif;font-size:.82rem;font-weight:700;cursor:pointer;transition:border-color .2s,color .2s;}
        .btn-close-soft:hover{border-color:#ccc;color:#999;}

        /* ══ SECTION 2: REAL LIFE — photo cards (SMALLER) ══ */
        .rl-block{margin-bottom:38px;}

        /* 4-column grid, smaller cards */
        .items-grid{
    display:grid;
    grid-template-columns:repeat(3, 1fr); /* 🔥 3 per row */
    gap:20px; /* spacing sa pagitan */
    padding:0 40px; /* 🔥 space sa gilid para pantay */
}
}
        @media(max-width:640px){.items-grid{grid-template-columns:repeat(3,1fr);gap:8px;}}
        @media(max-width:400px){.items-grid{grid-template-columns:repeat(2,1fr);}}

        /* Photo card — compact */
        .item-card{
            background:#fff;border-radius:12px;overflow:hidden;cursor:pointer;
            border:2px solid transparent;
            box-shadow:0 2px 8px rgba(0,0,0,.07);
            transition:transform .2s cubic-bezier(.34,1.56,.64,1),box-shadow .2s,border-color .2s;
            position:relative;
        }
        .item-card:hover{transform:translateY(-4px) scale(1.04);box-shadow:0 8px 18px rgba(0,0,0,.13);border-color:var(--ic);}
        .item-card:active{transform:scale(.95);}
        .item-card::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;background:var(--ic);z-index:2;}

        /* Photo wrapper — smaller, square */
        .item-photo{
            width:100%;
            aspect-ratio:1/1;
            overflow:hidden;
            position:relative;
            background:#f5f5f5;
        }
        .item-photo img{
            width:100%;height:100%;object-fit:cover;
            transition:transform .35s cubic-bezier(.34,1.56,.64,1);
            display:block;
        }
        .item-card:hover .item-photo img{transform:scale(1.08);}

        /* SVG fallback */
        .item-photo .item-svg-fallback{
            display:none;
            width:100%;height:100%;
            align-items:center;justify-content:center;
            padding:10px;
        }
        .item-photo .item-svg-fallback svg{width:100%;height:100%;filter:drop-shadow(0 3px 6px rgba(0,0,0,.12));}
        .item-photo img.broken + .item-svg-fallback{display:flex;}

        /* Name label — compact */
        .item-name{
            font-family:'Fredoka One',cursive;
            font-size:.72rem;
            color:#2d2d2d;
            text-align:center;
            padding:5px 4px 7px;
            line-height:1.2;
        }

        /* Real-life modal */
        .rl-modal{background:#fff;border-radius:26px;padding:0;text-align:center;max-width:360px;width:90%;box-shadow:0 22px 55px rgba(0,0,0,.18);animation:popUp .3s cubic-bezier(.34,1.56,.64,1) both;position:relative;overflow:hidden;}
        .rl-photo-wrap{width:100%;aspect-ratio:4/3;overflow:hidden;background:#f0f0f0;position:relative;}
        .rl-photo-wrap img{width:100%;height:100%;object-fit:cover;display:block;transition:transform .4s ease;}
        .rl-photo-wrap img:hover{transform:scale(1.04);}
        .rl-svg-fallback{display:none;position:absolute;inset:0;align-items:center;justify-content:center;padding:24px;}
        .rl-svg-fallback svg{width:100%;height:100%;}
        .rl-photo-wrap img.broken{display:none;}
        .rl-photo-wrap img.broken ~ .rl-svg-fallback{display:flex;}
        .rl-modal-body{padding:18px 22px 22px;}
        .rl-name{font-family:'Fredoka One',cursive;font-size:1.55rem;color:#2d2d2d;margin-bottom:5px;}
        .rl-fact{font-size:.87rem;color:#777;font-weight:600;line-height:1.5;margin-bottom:13px;}
        .rl-tag{display:inline-flex;align-items:center;gap:7px;background:#f5f5f5;border-radius:50px;padding:4px 14px;font-family:'Fredoka One',cursive;font-size:.78rem;color:#aaa;margin-bottom:15px;}
        .rl-tag svg{width:18px;height:18px;}

        /* ══ SECTION 3: HOW TO DRAW ══ */
        .draw-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:20px;}
        @media(max-width:768px){.draw-grid{grid-template-columns:repeat(2,1fr)}}
        .draw-card{background:#fff;border-radius:22px;padding:22px 16px 18px;text-align:center;cursor:pointer;position:relative;overflow:hidden;border:3px solid transparent;box-shadow:0 5px 16px rgba(0,0,0,.07);transition:transform .25s cubic-bezier(.34,1.56,.64,1),box-shadow .25s;animation:cardIn .5s ease both;}
        .draw-card:hover{transform:translateY(-8px) scale(1.03);box-shadow:0 18px 36px rgba(0,0,0,.12);}
        .draw-card:active{transform:scale(.96);}
        .draw-card::before{content:'';position:absolute;top:0;left:0;right:0;height:5px;background:var(--dc);border-radius:22px 22px 0 0;}
        .draw-card-svg{width:90px;height:90px;margin:0 auto 12px;}
        .draw-card-svg svg{width:100%;height:100%;filter:drop-shadow(0 5px 10px rgba(0,0,0,.13));}
        .draw-card-name{font-family:'Fredoka One',cursive;font-size:1.2rem;color:#2d2d2d;margin-bottom:4px;}
        .draw-card-hint{font-size:.75rem;color:#bbb;font-weight:700;}
        .draw-play-badge{position:absolute;bottom:12px;right:12px;width:32px;height:32px;border-radius:50%;background:var(--dc);display:flex;align-items:center;justify-content:center;box-shadow:0 3px 10px rgba(0,0,0,.15);}
        .draw-play-badge i{color:#fff;font-size:.8rem;margin-left:2px;}

        /* ══ DRAW MODAL ══ */
        .dm-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);backdrop-filter:blur(8px);z-index:999;align-items:center;justify-content:center;padding:16px;}
        .dm-overlay.active{display:flex;}
        .dm-modal{background:#fff;border-radius:28px;width:100%;max-width:480px;overflow:hidden;box-shadow:0 30px 80px rgba(0,0,0,.25);animation:popUp .35s cubic-bezier(.34,1.56,.64,1) both;}
        .dm-header{display:flex;align-items:center;gap:12px;padding:20px 22px 16px;border-bottom:2px solid #f5f5f5;}
        .dm-shape-preview{width:52px;height:52px;flex-shrink:0;}
        .dm-shape-preview svg{width:100%;height:100%;filter:drop-shadow(0 3px 8px rgba(0,0,0,.12));}
        .dm-htitle{font-family:'Fredoka One',cursive;font-size:1.5rem;color:#2d2d2d;}
        .dm-htip{font-size:.76rem;color:#bbb;font-weight:700;margin-top:2px;display:flex;align-items:center;gap:5px;}
        .dm-htip i{color:var(--stroke-color);}
        .dm-hclose{margin-left:auto;width:32px;height:32px;border-radius:50%;border:none;background:#f0f0f0;cursor:pointer;display:flex;align-items:center;justify-content:center;color:#888;font-size:.85rem;transition:background .2s,transform .2s;flex-shrink:0;}
        .dm-hclose:hover{background:#ffe0e0;transform:rotate(90deg);}
        .dm-notebook{background:#fdf8ef;position:relative;overflow:hidden;aspect-ratio:1/0.7;}
        .nb-svg{position:absolute;inset:0;width:100%;height:100%;}
        .stroke-path{fill:none;stroke:var(--stroke-color);stroke-width:5;stroke-linecap:round;stroke-linejoin:round;stroke-dasharray:600;stroke-dashoffset:600;opacity:0;transition:stroke-dashoffset .7s ease,opacity .1s;}
        .stroke-path.done{stroke-dashoffset:0;opacity:1;}
        .stroke-path.active{stroke-dashoffset:0;opacity:1;transition:stroke-dashoffset .7s ease,opacity .1s;}
        .ghost-path{fill:none;stroke:var(--stroke-color);stroke-width:3;stroke-dasharray:5 4;opacity:.12;pointer-events:none;}
        .dm-footer{padding:14px 22px;display:flex;align-items:center;justify-content:space-between;border-top:2px solid #f5f5f5;gap:10px;}
        .dm-hint{font-size:.8rem;color:#bbb;font-weight:700;display:flex;align-items:center;gap:6px;flex:1;}
        .dm-hint i{color:var(--stroke-color);}
        .dm-btns{display:flex;gap:8px;}
        .dm-btn{display:inline-flex;align-items:center;gap:6px;border:none;border-radius:50px;padding:9px 20px;font-family:'Fredoka One',cursive;font-size:.88rem;cursor:pointer;transition:transform .15s cubic-bezier(.34,1.56,.64,1),box-shadow .2s;}
        .dm-btn:hover{transform:scale(1.07);}
        .btn-replay{background:#f0f0f0;color:#888;}
        .btn-watch{background:var(--stroke-color);color:#fff;box-shadow:0 4px 12px rgba(0,0,0,.14);}
        .btn-watch:disabled{opacity:.5;cursor:not-allowed;transform:none;}
        .dm-steps{display:flex;justify-content:center;gap:7px;padding:12px 0 4px;}
        .step-dot{width:10px;height:10px;border-radius:50%;background:#e8e8e8;transition:background .3s,transform .2s;}
        .step-dot.done{background:var(--stroke-color);}
        .step-dot.active{background:var(--stroke-color);transform:scale(1.35);}

        /* ══ SECTION 4: SHAPE TRACING ══ */
        .trace-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:20px;}
        @media(max-width:768px){.trace-grid{grid-template-columns:repeat(2,1fr)}}
        .trace-card{background:#fff;border-radius:22px;padding:18px 12px 14px;text-align:center;cursor:pointer;position:relative;overflow:hidden;border:2.5px dashed var(--tc,#FF6B6B);box-shadow:0 5px 16px rgba(0,0,0,.07);transition:transform .25s cubic-bezier(.34,1.56,.64,1),box-shadow .25s,border-style .2s;}
        .trace-card:hover{transform:translateY(-6px) scale(1.03);box-shadow:0 16px 32px rgba(0,0,0,.12);border-style:solid;}
        .trace-card::before{display:none;}
        .trace-card-svg{width:80px;height:80px;margin:0 auto 10px;}
        .trace-card-svg svg{width:100%;height:100%;}
        .trace-card-name{font-family:'Fredoka One',cursive;font-size:1.1rem;color:#2d2d2d;margin-bottom:3px;}
        .trace-card-hint{font-size:.73rem;color:#bbb;font-weight:700;}
        .trace-badge{position:absolute;bottom:10px;right:10px;width:30px;height:30px;border-radius:50%;background:var(--tc);display:flex;align-items:center;justify-content:center;}
        .trace-badge i{color:#fff;font-size:.75rem;}

        /* ══ TRACE MODAL ══ */
        .tm-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);backdrop-filter:blur(8px);z-index:1000;align-items:center;justify-content:center;padding:12px;}
        .tm-overlay.active{display:flex;}
        .tm-modal{background:#fff;border-radius:28px;width:100%;max-width:520px;overflow:hidden;box-shadow:0 30px 80px rgba(0,0,0,.25);animation:popUp .35s cubic-bezier(.34,1.56,.64,1) both;}
        .tm-header{display:flex;align-items:center;gap:12px;padding:18px 20px 14px;border-bottom:2px solid #f5f5f5;}
        .tm-preview{width:48px;height:48px;flex-shrink:0;}
        .tm-preview svg{width:100%;height:100%;}
        .tm-title{font-family:'Fredoka One',cursive;font-size:1.4rem;color:#2d2d2d;}
        .tm-sub{font-size:.74rem;color:#bbb;font-weight:700;margin-top:2px;}
        .tm-close{margin-left:auto;width:32px;height:32px;border-radius:50%;border:none;background:#f0f0f0;cursor:pointer;display:flex;align-items:center;justify-content:center;color:#888;font-size:.85rem;transition:background .2s,transform .2s;flex-shrink:0;}
        .tm-close:hover{background:#ffe0e0;transform:rotate(90deg);}
        .tm-canvas-wrap{background:#fef9f0;position:relative;display:flex;align-items:center;justify-content:center;padding:20px;border-bottom:2px solid #f5f5f5;}
        .tm-canvas-wrap canvas{border-radius:16px;cursor:crosshair;touch-action:none;background:#fff;box-shadow:0 4px 18px rgba(0,0,0,.08);}
        .tm-guide-label{position:absolute;top:26px;left:50%;transform:translateX(-50%);font-family:'Fredoka One',cursive;font-size:.8rem;color:#ccc;white-space:nowrap;pointer-events:none;}
        .tm-footer{padding:14px 20px;display:flex;align-items:center;justify-content:space-between;gap:10px;}
        .tm-score{font-family:'Fredoka One',cursive;font-size:1rem;color:#aaa;}
        .tm-score span{color:var(--trace-color,#FF6B6B);font-size:1.3rem;}
        .tm-btns{display:flex;gap:8px;}
        .tm-btn{display:inline-flex;align-items:center;gap:6px;border:none;border-radius:50px;padding:9px 18px;font-family:'Fredoka One',cursive;font-size:.85rem;cursor:pointer;transition:transform .15s cubic-bezier(.34,1.56,.64,1);}
        .tm-btn:hover{transform:scale(1.07);}
        .tm-clear{background:#f0f0f0;color:#888;}
        .tm-check{background:var(--trace-color,#FF6B6B);color:#fff;box-shadow:0 4px 12px rgba(0,0,0,.14);}

        /* ══ SECTION 5: SHAPE QUIZ ══ */
        .quiz-wrap{background:#fff;border-radius:24px;padding:28px;box-shadow:0 6px 20px rgba(0,0,0,.07);}
        .quiz-question-area{text-align:center;margin-bottom:22px;}
        .quiz-q-label{font-family:'Fredoka One',cursive;font-size:1.15rem;color:#555;margin-bottom:10px;}
        .quiz-object-display{width:160px;height:160px;margin:0 auto 6px;filter:drop-shadow(0 10px 24px rgba(0,0,0,.18));animation:floaty 2s ease-in-out infinite;border-radius:24px;background:#f8f8f8;display:flex;align-items:center;justify-content:center;padding:10px;}
        .quiz-object-display svg{width:100%;height:100%;}
        .quiz-object-label{font-family:'Fredoka One',cursive;font-size:1.1rem;color:#aaa;margin-bottom:16px;}
        .quiz-choices{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;}
        @media(max-width:600px){.quiz-choices{grid-template-columns:repeat(2,1fr)}}
        .quiz-choice{background:#f8f8f8;border:3px solid #eee;border-radius:18px;padding:14px 8px 12px;text-align:center;cursor:pointer;transition:transform .2s cubic-bezier(.34,1.56,.64,1),border-color .2s,background .2s;position:relative;overflow:hidden;}
        .quiz-choice:hover{transform:translateY(-4px) scale(1.04);border-color:#ddd;background:#fff;}
        .quiz-choice.correct{background:#e8f5e9;border-color:#4caf50;animation:correctPop .5s cubic-bezier(.34,1.56,.64,1);}
        .quiz-choice.wrong{background:#ffebee;border-color:#ef5350;animation:wrongShake .4s ease;}
        .quiz-choice.wrong::after{content:'✕';position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);font-size:2.8rem;font-weight:900;color:#ef5350;opacity:.55;pointer-events:none;line-height:1;}
        @keyframes correctPop{0%{transform:scale(1)}40%{transform:scale(1.12)}100%{transform:scale(1)}}
        @keyframes wrongShake{0%,100%{transform:translateX(0)}25%{transform:translateX(-8px)}75%{transform:translateX(8px)}}
        .quiz-choice-svg{width:64px;height:64px;margin:0 auto 8px;}
        .quiz-choice-svg svg{width:100%;height:100%;}
        .quiz-choice-name{font-family:'Fredoka One',cursive;font-size:.95rem;color:#2d2d2d;}
        .quiz-nav{display:flex;align-items:center;justify-content:center;gap:12px;margin-top:20px;}
        .quiz-nav-btn{display:inline-flex;align-items:center;gap:7px;border:none;border-radius:50px;padding:10px 24px;font-family:'Fredoka One',cursive;font-size:.9rem;cursor:pointer;transition:transform .2s;background:var(--green-dark);color:#fff;box-shadow:0 4px 14px rgba(13,84,7,.2);}
        .quiz-nav-btn:hover{transform:scale(1.06);}
        .quiz-nav-btn:disabled{opacity:.4;cursor:not-allowed;transform:none;}
        .quiz-done-panel{display:none;text-align:center;padding:20px 0;}
        .quiz-done-icon{font-size:4rem;margin-bottom:12px;animation:floaty 2s ease-in-out infinite;}
        .quiz-done-title{font-family:'Fredoka One',cursive;font-size:1.8rem;color:var(--green-dark);margin-bottom:6px;}
        .quiz-done-sub{font-size:.95rem;color:#aaa;font-weight:700;margin-bottom:18px;}
        .quiz-restart-btn{display:inline-flex;align-items:center;gap:8px;background:var(--green-dark);color:#fff;border:none;border-radius:50px;padding:12px 28px;font-family:'Fredoka One',cursive;font-size:1rem;cursor:pointer;box-shadow:0 6px 18px rgba(13,84,7,.25);transition:transform .2s;}
        .quiz-restart-btn:hover{transform:scale(1.06);}

        /* ══ SECTION 6: FIND THE SHAPE ══ */
        .find-wrap{background:#fff;border-radius:24px;padding:26px;box-shadow:0 6px 20px rgba(0,0,0,.07);}
        .find-topbar{display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;flex-wrap:wrap;gap:10px;}
        .find-mission{background:linear-gradient(135deg,#1a237e,#3949ab);color:#fff;border-radius:18px;padding:14px 20px;display:flex;align-items:center;gap:14px;margin-bottom:16px;}
        .find-mission-svg{width:56px;height:56px;flex-shrink:0;filter:drop-shadow(0 4px 10px rgba(0,0,0,.2));}
        .find-mission-svg svg{width:100%;height:100%;}
        .find-mission-text{flex:1;}
        .find-mission-label{font-family:'Fredoka One',cursive;font-size:.78rem;opacity:.7;text-transform:uppercase;letter-spacing:.08em;margin-bottom:3px;}
        .find-mission-name{font-family:'Fredoka One',cursive;font-size:1.4rem;line-height:1.1;}
        .find-mission-sub{font-size:.8rem;opacity:.75;font-weight:700;margin-top:2px;}
        .find-stats{display:flex;gap:14px;flex-wrap:wrap;}
        .find-stat{background:#f8f8f8;border-radius:14px;padding:10px 18px;font-family:'Fredoka One',cursive;font-size:.9rem;color:#aaa;display:flex;align-items:center;gap:8px;}
        .find-stat strong{color:#2d2d2d;font-size:1.1rem;}
        .find-next-btn{display:inline-flex;align-items:center;gap:7px;border:none;border-radius:50px;padding:10px 22px;font-family:'Fredoka One',cursive;font-size:.88rem;cursor:pointer;transition:transform .2s;background:var(--green-dark);color:#fff;box-shadow:0 4px 14px rgba(13,84,7,.2);}
        .find-next-btn:hover{transform:scale(1.06);}
        .playground-scene{position:relative;width:100%;border-radius:20px;overflow:hidden;border:3px solid #b6d9b6;aspect-ratio:16/9;background:#87CEEB;margin-bottom:14px;}
        .pg-sky{position:absolute;inset:0;background:linear-gradient(180deg,#87CEEB 0%,#B8E0FF 100%);pointer-events:none;}
        .pg-ground{position:absolute;bottom:0;left:0;right:0;height:30%;background:#7ec850;border-top:4px solid #5aaa2a;pointer-events:none;}
        .pg-cloud{position:absolute;pointer-events:none;}
        .pg-obj{position:absolute;cursor:pointer;transform-origin:center bottom;transition:transform .18s cubic-bezier(.34,1.56,.64,1),opacity .45s ease,filter .15s;user-select:none;}
        .pg-obj:hover{filter:brightness(1.08);}
        .pg-obj.found{opacity:0;transform:scale(.3) translateY(-30px) !important;pointer-events:none;transition:opacity .4s ease,transform .4s cubic-bezier(.34,1.56,.64,1) !important;}
        .pg-obj.wrong-shake{animation:pgWrong .35s ease;}
        @keyframes pgWrong{0%,100%{transform:translateX(0)}25%{transform:translateX(-8px)}75%{transform:translateX(8px)}}
        .wrong-x-pop{position:absolute;top:50%;left:50%;transform:translate(-50%,-50%) scale(0);font-size:2.2rem;font-weight:900;color:#ef5350;pointer-events:none;z-index:20;animation:xPop .65s cubic-bezier(.34,1.56,.64,1) forwards;}
        @keyframes xPop{0%{transform:translate(-50%,-50%) scale(0);opacity:1}45%{transform:translate(-50%,-50%) scale(1.3);opacity:1}100%{transform:translate(-50%,-50%) scale(.9);opacity:0}}
        .find-chips-row{display:flex;gap:8px;flex-wrap:wrap;margin-top:4px;}
        .find-chip{background:#f8f8f8;border:2px solid #eee;border-radius:50px;padding:5px 14px 5px 9px;display:flex;align-items:center;gap:7px;font-family:'Fredoka One',cursive;font-size:.78rem;color:#aaa;transition:background .3s,border-color .3s,color .3s;}
        .find-chip.found-chip{background:#e8f5e9;border-color:#4caf50;color:#2e7d32;}
        .find-done-banner{display:none;text-align:center;padding:16px;background:linear-gradient(135deg,#e8f5e9,#c8e6c9);border-radius:16px;margin-top:12px;}
        .find-done-banner.show{display:block;}
        .find-done-banner .fdb-title{font-family:'Fredoka One',cursive;font-size:1.4rem;color:#2e7d32;margin-bottom:4px;}
        .find-done-banner .fdb-sub{font-size:.85rem;color:#388e3c;font-weight:700;}

        /* ══ SECTION 7: VIDEOS ══ */
        .video-section-wrap{background:#fff;border-radius:24px;overflow:hidden;box-shadow:0 6px 20px rgba(0,0,0,.07);margin-bottom:60px;}
        .video-section-header{display:flex;align-items:center;gap:16px;padding:20px 24px;border-bottom:2px solid #f5f5f5;}
        .vid-sec-icon{width:52px;height:52px;border-radius:14px;background:linear-gradient(135deg,#e040fb,#7c4dff);display:flex;align-items:center;justify-content:center;box-shadow:0 4px 14px rgba(124,77,255,.3);flex-shrink:0;}
        .vid-sec-icon i{color:#fff;font-size:1.3rem;}
        .vid-sec-title{font-family:'Fredoka One',cursive;font-size:1.35rem;color:#1a1a2e;margin:0;}
        .vid-sec-sub{font-size:.8rem;color:#bbb;font-weight:700;margin-top:2px;}
        .video-tabs{display:flex;gap:8px;padding:16px 24px 0;}
        .vid-tab{display:inline-flex;align-items:center;gap:7px;padding:9px 20px;border-radius:50px;border:2px solid #eee;background:#f8f8f8;font-family:'Fredoka One',cursive;font-size:.85rem;color:#aaa;cursor:pointer;transition:all .2s cubic-bezier(.34,1.56,.64,1);}
        .vid-tab:hover{transform:scale(1.04);border-color:#ddd;}
        .vid-tab.active{background:linear-gradient(135deg,#7c4dff,#e040fb);color:#fff;border-color:transparent;box-shadow:0 4px 14px rgba(124,77,255,.3);}
        .vid-tab i{font-size:.8rem;}
        .video-player-wrap{padding:16px 24px 24px;}
        .video-embed{width:100%;aspect-ratio:16/9;border-radius:16px;overflow:hidden;background:#111;position:relative;}
        .video-embed iframe{width:100%;height:100%;border:none;display:block;}

        /* ══ SHARED ══ */
        .screen-flash{position:fixed;inset:0;pointer-events:none;z-index:9998;opacity:0;transition:opacity .15s;}
        .screen-flash.flash-green{background:rgba(76,175,80,.22);}
        .screen-flash.flash-red{background:rgba(239,83,80,.22);}
        .screen-flash.show{opacity:1;}
        .cp{position:fixed;pointer-events:none;z-index:9999;animation:fall linear forwards;border-radius:2px;}
        @keyframes fall{0%{transform:translateY(-20px) rotate(0deg);opacity:1}100%{transform:translateY(100vh) rotate(720deg);opacity:0}}
    </style>
</head>
<body>

<div class="screen-flash" id="screenFlash"></div>

<nav class="lesson-nav">
    <a href="lessons.php" class="lnav-back"><i class="fas fa-arrow-left"></i> Back</a>
    <div style="flex:1"></div>
    <div class="lang-toggle">
        <button class="lang-btn active" id="btnEN" onclick="setLang('en')">EN</button>
        <button class="lang-btn"        id="btnTL" onclick="setLang('tl')">TL</button>
    </div>
</nav>

<div class="container">

    <div class="page-header">
        <h1 id="pageTitle">Shapes Explorer</h1>
        <div class="lang-badge" id="langBadge">
            <span id="langBadgeFlag">EN</span>
            <span id="langBadgeText">English &mdash; 8 Shapes</span>
        </div>
    </div>

    <!-- ══ SECTION 1: LEARN ══ -->
    <div class="section-header-card">
        <div class="sec-icon-box" style="background:linear-gradient(135deg,#0d5407,#3d8b37);">
            <svg viewBox="0 0 100 100"><path d="M50,10 A40,40 0 1,1 49.99,10 Z" fill="#fff"/></svg>
        </div>
        <div class="sec-title-group">
            <div class="sec-title" id="sec1label">Learn the Shapes</div>
            <div class="sec-subtitle" id="sec1sub">Tap a shape!</div>
        </div>
        <div class="sec-line"></div>
    </div>
    <div class="shapes-grid" id="shapesGrid"></div>

    <!-- ══ SECTION 2: REAL LIFE ══ -->
    <div class="section-header-card">
        <div class="sec-icon-box" style="background:linear-gradient(135deg,#ff922b,#ffd43b);">
            <svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                <circle cx="50" cy="38" r="22" fill="#fff"/>
                <path d="M20,62 Q50,85 80,62" fill="none" stroke="#fff" stroke-width="6" stroke-linecap="round"/>
                <line x1="50" y1="60" x2="50" y2="80" stroke="#fff" stroke-width="5" stroke-linecap="round"/>
            </svg>
        </div>
        <div class="sec-title-group">
            <div class="sec-title" id="sec2label">Shapes Around Us</div>
            <div class="sec-subtitle" id="sec2sub">Tap an object!</div>
        </div>
        <div class="sec-line"></div>
    </div>

    <?php
    $ri = 0;
    foreach ($reallife as $rlColor => [$rlPath, $rlItems]):
        $enName = $shapes_en[$ri][0];
        $tlName = $shapes_tl[$ri][0];
    ?>
    <div class="rl-block">
        <div class="rl-hdr">
            <div class="rl-shape-dot" style="background:<?= $rlColor ?>; box-shadow:0 2px 6px <?= $rlColor ?>55"></div>
            <div class="rl-hdr-name">
                <span class="rl-sname" data-en="<?= $enName ?>" data-tl="<?= $tlName ?>"><?= $enName ?></span>
                <small class="rl-stap">Tap!</small>
            </div>
            <div class="rl-divline" style="--rc:<?= $rlColor ?>"></div>
        </div>
        <div class="items-grid">
            <?php foreach ($rlItems as [$enN,$enF,$tlN,$tlF,$svg,$imgPath]): ?>
            <div class="item-card" style="--ic:<?= $rlColor ?>"
                 data-en-name="<?= htmlspecialchars($enN) ?>"
                 data-en-fact="<?= htmlspecialchars($enF) ?>"
                 data-tl-name="<?= htmlspecialchars($tlN) ?>"
                 data-tl-fact="<?= htmlspecialchars($tlF) ?>"
                 data-color="<?= $rlColor ?>"
                 data-path="<?= htmlspecialchars($rlPath) ?>"
                 data-shape-en="<?= $enName ?>"
                 data-shape-tl="<?= $tlName ?>"
                 data-img="<?= htmlspecialchars($imgPath) ?>"
                 data-svg="<?= htmlspecialchars($svg) ?>"
                 onclick="openRL(this)">
                <div class="item-photo">
                    <img
                        src="<?= htmlspecialchars($imgPath) ?>"
                        alt="<?= htmlspecialchars($enN) ?>"
                        loading="lazy"
                        onerror="this.classList.add('broken')">
                    <div class="item-svg-fallback">
                        <svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg"><?= $svg ?></svg>
                    </div>
                </div>
                <div class="item-name rl-item-label" data-en="<?= htmlspecialchars($enN) ?>" data-tl="<?= htmlspecialchars($tlN) ?>"><?= $enN ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php $ri++; endforeach; ?>

    <!-- ══ SECTION 3: HOW TO DRAW ══ -->
    <div class="section-header-card">
        <div class="sec-icon-box" style="background:linear-gradient(135deg,#7b3fa0,#c060e8);">
            <i class="fas fa-pencil-ruler"></i>
        </div>
        <div class="sec-title-group">
            <div class="sec-title" id="sec3label">How to Draw</div>
            <div class="sec-subtitle" id="sec3sub">Tap and watch!</div>
        </div>
        <div class="sec-line"></div>
    </div>

    <div class="draw-grid">
        <?php $di=0; foreach ($drawShapes as $dName => [$dColor,$dTipEN,$dTipTL,$dStrokes]): ?>
        <div class="draw-card" style="--dc:<?= $dColor ?>;animation-delay:<?= $di*.06 ?>s"
             data-idx="<?= $di ?>"
             onclick="openDrawModal(<?= $di ?>)">
            <div class="draw-card-svg">
                <svg viewBox="0 0 100 100">
                    <path d="<?= $shapePaths[$di] ?>" fill="<?= $dColor ?>"/>
                </svg>
            </div>
            <div class="draw-card-name draw-cname" data-en="<?= $dName ?>" data-tl="<?= $shapes_tl[$di][0] ?>"><?= $dName ?></div>
            <div class="draw-card-hint" id="dcardtip-<?= $di ?>">Watch!</div>
            <div class="draw-play-badge"><i class="fas fa-play"></i></div>
        </div>
        <?php $di++; endforeach; ?>
    </div>

    <!-- ══ NOW LET'S PLAY DIVIDER ══ -->
    <div class="lets-play-divider">
        <div class="lets-play-pill">
            <span class="pill-icon">&#127918;</span>
            <span id="letsPlayLabel">Now let's play!</span>
        </div>
    </div>

    <!-- ══ SECTION 4: SHAPE TRACING ══ -->
    <div class="section-header-card">
        <div class="sec-icon-box" style="background:linear-gradient(135deg,#e91e63,#ff6090);">
            <i class="fas fa-pen-nib"></i>
        </div>
        <div class="sec-title-group">
            <div class="sec-title" id="sec4label">Trace the Shape!</div>
            <div class="sec-subtitle" id="sec4sub">Draw over the dots!</div>
        </div>
        <div class="sec-line"></div>
    </div>
    <div class="trace-grid" id="traceGrid"></div>

    <!-- ══ SECTION 5: SHAPE QUIZ ══ -->
    <div class="section-header-card">
        <div class="sec-icon-box" style="background:linear-gradient(135deg,#f57c00,#ffb300);">
            <i class="fas fa-trophy"></i>
        </div>
        <div class="sec-title-group">
            <div class="sec-title" id="sec5label">Shape Quiz!</div>
            <div class="sec-subtitle" id="sec5sub">What shape is this object?</div>
        </div>
        <div class="sec-line"></div>
    </div>

    <div class="quiz-wrap" id="quizWrap">
        <div id="quizMainArea">
            <div class="quiz-question-area">
                <div class="quiz-q-label" id="quizQLabel">What shape is this?</div>
                <div class="quiz-object-display" id="quizObjectDisplay"></div>
                <div class="quiz-object-label" id="quizObjectLabel"></div>
            </div>
            <div class="quiz-choices" id="quizChoices"></div>
            <div class="quiz-nav">
                <button class="quiz-nav-btn" id="quizNextBtn" onclick="quizNext()" disabled>
                    <span id="quizNextLabel">Next</span> <i class="fas fa-arrow-right"></i>
                </button>
            </div>
        </div>
        <div class="quiz-done-panel" id="quizDone">
            <div class="quiz-done-icon">&#127942;</div>
            <div class="quiz-done-title" id="quizDoneTitle">Quiz Done!</div>
            <div class="quiz-done-sub" id="quizDoneSub"></div>
            <button class="quiz-restart-btn" onclick="quizRestart()">
                <i class="fas fa-rotate-left"></i><span id="quizRestartLabel">Play Again!</span>
            </button>
        </div>
    </div>

    <!-- ══ SECTION 6: FIND THE SHAPE ══ -->
    <div class="section-header-card">
        <div class="sec-icon-box" style="background:linear-gradient(135deg,#0288d1,#26c6da);">
            <i class="fas fa-magnifying-glass"></i>
        </div>
        <div class="sec-title-group">
            <div class="sec-title" id="sec6label">Find the Shape!</div>
            <div class="sec-subtitle" id="sec6sub">Tap the right objects!</div>
        </div>
        <div class="sec-line"></div>
    </div>

    <div class="find-wrap" id="findWrap">
        <div class="find-topbar">
            <div class="find-stats">
                <div class="find-stat"><i class="fas fa-check-circle" style="color:#4caf50"></i> <span id="findFoundLabel">Found</span> <strong id="findFound">0</strong></div>
                <div class="find-stat"><i class="fas fa-shapes" style="color:#9c27b0"></i> <span id="findTotalLabel">Total</span> <strong id="findTotal">0</strong></div>
            </div>
            <button class="find-next-btn" id="findNextBtn" onclick="findNextRound()">
                <i class="fas fa-forward"></i> <span id="findNextLabel">Next Round</span>
            </button>
        </div>
        <div class="find-mission" id="findMission">
            <div class="find-mission-svg" id="findMissionSvg"></div>
            <div class="find-mission-text">
                <div class="find-mission-label" id="findMissionLabel">Find all the...</div>
                <div class="find-mission-name" id="findMissionName">Circle</div>
                <div class="find-mission-sub" id="findMissionSub">Tap them in the playground!</div>
            </div>
        </div>
        <div class="playground-scene" id="playgroundScene">
            <div class="pg-sky"></div>
            <div class="pg-ground"></div>
        </div>
        <div class="find-chips-row" id="findChipsRow"></div>
        <div class="find-done-banner" id="findDoneBanner">
            <div class="fdb-title" id="findDoneTitle">&#127881; All Found!</div>
            <div class="fdb-sub" id="findDoneSub">Amazing job! Tap Next Round for more.</div>
        </div>
    </div>

    <!-- ══ SECTION 7: VIDEOS ══ -->
    <div class="section-header-card">
        <div class="sec-icon-box" style="background:linear-gradient(135deg,#e040fb,#7c4dff);">
            <i class="fas fa-music"></i>
        </div>
        <div class="sec-title-group">
            <div class="sec-title" id="sec7label">Sing the Shapes Song!</div>
            <div class="sec-subtitle" id="sec7sub">Watch and sing along &mdash; tap a video to play it!</div>
        </div>
        <div class="sec-line"></div>
    </div>

    <div class="video-section-wrap">
        <div class="video-tabs" id="videoTabs"></div>
        <div class="video-player-wrap">
            <div class="video-embed" id="videoEmbed">
                <iframe id="videoIframe" src="" allowfullscreen allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"></iframe>
            </div>
        </div>
    </div>

    <div style="height:60px"></div>
</div><!-- /container -->

<!-- ══ LEARN MODAL ══ -->
<div class="overlay" id="learnOverlay" onclick="if(event.target===this)closeLM()">
    <div class="lm-modal" id="learnModal">
        <button class="mx" onclick="closeLM()"><i class="fas fa-times"></i></button>
        <svg class="lm-svg" viewBox="0 0 100 100"><path id="lmPath" d="" fill="#4ECDC4"/></svg>
        <div class="lm-name"  id="lmName"></div>
        <div class="lm-local" id="lmLocal"></div>
        <div class="lm-desc"  id="lmDesc"></div>
        <button class="speak-btn" id="speakBtn" onclick="speakIt()">
            <i class="fas fa-volume-high"></i><span id="speakLabel">Say it!</span>
        </button><br>
        <button class="btn-close-soft" id="lmClose" onclick="closeLM()">OK!</button>
    </div>
</div>

<!-- ══ REAL LIFE MODAL ══ -->
<div class="overlay" id="rlOverlay" onclick="if(event.target===this)closeRL()">
    <div class="rl-modal">
        <button class="mx" onclick="closeRL()" style="z-index:2;background:rgba(255,255,255,.9)"><i class="fas fa-times"></i></button>
        <div class="rl-photo-wrap" id="rlPhotoWrap">
            <img id="rlModalImg" src="" alt="" onerror="this.classList.add('broken')">
            <div class="rl-svg-fallback" id="rlSvgFallback">
                <svg viewBox="0 0 100 100" id="rlFallbackSvg"></svg>
            </div>
        </div>
        <div class="rl-modal-body">
            <div class="rl-name" id="rlName"></div>
            <div class="rl-fact" id="rlFact"></div>
            <div class="rl-tag">
                <svg viewBox="0 0 100 100"><path id="rlTagPath" d=""/></svg>
                <span id="rlTagLabel"></span>
            </div><br>
            <button class="btn-close-soft" onclick="closeRL()">OK!</button>
        </div>
    </div>
</div>

<!-- ══ DRAW MODAL ══ -->
<div class="dm-overlay" id="dmOverlay" onclick="if(event.target===this)closeDM()">
    <div class="dm-modal" style="--stroke-color:#FF6B6B" id="dmModal">
        <div class="dm-header">
            <div class="dm-shape-preview">
                <svg viewBox="0 0 100 100"><path id="dmPreviewPath" d="" fill="#FF6B6B"/></svg>
            </div>
            <div>
                <div class="dm-htitle" id="dmTitle">Circle</div>
                <div class="dm-htip"><i class="fas fa-eye"></i><span id="dmTip">Watch!</span></div>
            </div>
            <button class="dm-hclose" onclick="closeDM()"><i class="fas fa-times"></i></button>
        </div>
        <div class="dm-notebook" id="dmNotebook">
            <svg class="nb-svg" id="dmNbSvg" viewBox="0 0 480 336" preserveAspectRatio="xMidYMid meet">
                <?php for($ln=1;$ln<=8;$ln++): $y=$ln*38; ?>
                <line x1="30" y1="<?=$y?>" x2="460" y2="<?=$y?>" stroke="#ddd" stroke-width="1"/>
                <?php endfor; ?>
                <line x1="54" y1="0" x2="54" y2="336" stroke="#f0c0c0" stroke-width="1.5" opacity=".6"/>
                <g transform="translate(140,38) scale(2.6)">
                    <path class="ghost-path" id="dmGhost" d=""/>
                </g>
                <g transform="translate(140,38) scale(2.6)" id="dmStrokesGroup"></g>
            </svg>
        </div>
        <div class="dm-steps" id="dmDots"></div>
        <div class="dm-footer">
            <div class="dm-hint"><i class="fas fa-lightbulb"></i><span id="dmFooterHint">Press Watch!</span></div>
            <div class="dm-btns">
                <button class="dm-btn btn-replay" onclick="replayDM()"><i class="fas fa-rotate-left"></i> <span id="dmReplayLabel">Replay</span></button>
                <button class="dm-btn btn-watch"  onclick="nextStrokeDM()" id="dmNextBtn"><i class="fas fa-play"></i> <span id="dmWatchLabel">Watch!</span></button>
            </div>
        </div>
    </div>
</div>

<!-- ══ TRACE MODAL ══ -->
<div class="tm-overlay" id="tmOverlay" onclick="if(event.target===this)closeTM()">
    <div class="tm-modal" id="tmModal">
        <div class="tm-header">
            <div class="tm-preview"><svg viewBox="0 0 100 100"><path id="tmPreviewPath" d="" fill="#FF6B6B"/></svg></div>
            <div>
                <div class="tm-title" id="tmTitle">Circle</div>
                <div class="tm-sub" id="tmSub">Draw over the dots!</div>
            </div>
            <button class="tm-close" onclick="closeTM()"><i class="fas fa-times"></i></button>
        </div>
        <div class="tm-canvas-wrap">
            <div class="tm-guide-label" id="tmGuideLabel">Draw over the dots!</div>
            <canvas id="traceCanvas" width="340" height="280"></canvas>
        </div>
        <div class="tm-footer">
            <div class="tm-score" id="tmScoreArea"><span id="tmCoverageNum">0</span>%</div>
            <div class="tm-btns">
                <button class="tm-btn tm-clear" onclick="clearTrace()"><i class="fas fa-eraser"></i> <span id="tmClearLabel">Clear</span></button>
                <button class="tm-btn tm-check" id="tmCheckBtn" onclick="checkTrace()" style="--trace-color:#FF6B6B"><i class="fas fa-check"></i> <span id="tmCheckLabel">Check!</span></button>
            </div>
        </div>
    </div>
</div>

<script>
/* ══ SOUND ENGINE ══ */
const AudioCtx=window.AudioContext||window.webkitAudioContext;
let _actx=null;
function getACtx(){if(!_actx)_actx=new AudioCtx();return _actx;}
function playCorrectSound(){try{const ctx=getACtx(),t=ctx.currentTime;[523.25,783.99].forEach((freq,i)=>{const osc=ctx.createOscillator(),gain=ctx.createGain();osc.connect(gain);gain.connect(ctx.destination);osc.type='sine';osc.frequency.setValueAtTime(freq,t+i*.12);gain.gain.setValueAtTime(0,t+i*.12);gain.gain.linearRampToValueAtTime(.35,t+i*.12+.02);gain.gain.exponentialRampToValueAtTime(.001,t+i*.12+.35);osc.start(t+i*.12);osc.stop(t+i*.12+.4);});}catch(e){}}
function playWrongSound(){try{const ctx=getACtx(),t=ctx.currentTime;const osc=ctx.createOscillator(),gain=ctx.createGain();osc.connect(gain);gain.connect(ctx.destination);osc.type='sawtooth';osc.frequency.setValueAtTime(180,t);osc.frequency.linearRampToValueAtTime(80,t+.22);gain.gain.setValueAtTime(.28,t);gain.gain.exponentialRampToValueAtTime(.001,t+.28);osc.start(t);osc.stop(t+.3);}catch(e){}}
function flashScreen(type){const el=document.getElementById('screenFlash');el.className='screen-flash flash-'+type+' show';setTimeout(()=>el.classList.remove('show'),300);}

/* ══ SHARED DATA ══ */
const SHAPES_EN=<?php echo json_encode(array_values($shapes_en),JSON_UNESCAPED_UNICODE);?>;
const SHAPES_TL=<?php echo json_encode(array_values($shapes_tl),JSON_UNESCAPED_UNICODE);?>;
const SHAPE_META=<?php $meta=[];foreach($shapes as $k=>$v)$meta[]=['color'=>$v[0],'path'=>$v[1]];echo json_encode($meta);?>;
const DRAW_DATA=<?php
$dd=[];
foreach($drawShapes as $k=>$v){
    $dd[]=['name'=>$k,'color'=>$v[0],'tipEN'=>$v[1],'tipTL'=>$v[2],'strokes'=>$v[3],'path'=>$shapePaths[array_search($k,$drawKeys)]];
}
echo json_encode($dd);
?>;
const QUIZ_ITEMS=<?php echo json_encode(array_values($quizItems),JSON_UNESCAPED_UNICODE);?>;
const TL_NAMES=<?php echo json_encode(array_column($shapes_tl,0));?>;
const FIND_ROUNDS=<?php
$fr=[];
foreach($findRoundsData as $r){
    $objs=[];
    foreach($r['objects'] as $o){
        $objs[]=['nameEN'=>$o[0],'nameTL'=>$o[1],'svg'=>$o[2],'isTarget'=>$o[3],'x'=>$o[4],'y'=>$o[5],'size'=>$o[6]];
    }
    $fr[]=['nameEN'=>$r['name'],'nameTL'=>$r['nameTL'],'color'=>$r['color'],'path'=>$r['path'],'findCount'=>$r['findCount'],'objects'=>$objs];
}
echo json_encode($fr,JSON_UNESCAPED_UNICODE);
?>;

const VIDEOS={
    en:[
        {labelEN:'Shapes Song',labelTL:'Shapes Song',icon:'fa-music',id:'peSNdpGC14Q'},
        {labelEN:'Learn Shapes',labelTL:'Alamin Hugis',icon:'fa-shapes',id:'fkYNQ-_Xck8'},
    ],
    tl:[
        {labelEN:'Mga Hugis',labelTL:'Mga Hugis',icon:'fa-music',id:'_d-HhPFJ6a8'},
        {labelEN:'Hugis Song',labelTL:'Hugis Song',icon:'fa-star',id:'d-Vb4-VJW8s'},
    ],
};

const UI={
    en:{title:'Shapes Explorer',badgeFlag:'EN',badgeText:'English \u2014 8 Shapes',sec1:'Learn the Shapes',sec1sub:'Tap a shape!',sec2:'Shapes Around Us',sec2sub:'Tap an object!',sec3:'How to Draw',sec3sub:'Tap and watch!',sec4:'Trace the Shape!',sec4sub:'Draw over the dots!',sec5:'Shape Quiz!',sec5sub:'What shape is this object?',sec6:'Find the Shape!',sec6sub:'Tap the right objects!',sec7:'Sing the Shapes Song!',sec7sub:'Watch and sing along \u2014 tap a video to play it!',closeBtn:'OK!',speak:'Say it!',tap:'Tap!',tapDraw:'Watch!',watch:'Watch!',replay:'Replay',hint:'Press Watch!',traceHint:'Draw over the dots!',traceClear:'Clear',traceCheck:'Check!',traceTap:'Tap to trace!',quizQ:'What shape is this?',quizNext:'Next',quizRestart:'Play Again!',quizDoneTitle:'Quiz Done!',findFound:'Found',findTotal:'Total',findMissionLabel:'Find all the...',findMissionSub:'Tap them in the playground!',findNextRound:'Next Round',findDoneTitle:'\uD83C\uDF89 All Found!',findDoneSub:'Amazing job! Tap Next Round for more.',letsPlay:"Now let's play!"},
    tl:{title:'Alamin ang mga Hugis',badgeFlag:'TL',badgeText:'Filipino \u2014 8 Hugis',sec1:'Alamin ang Hugis',sec1sub:'I-tap ang hugis!',sec2:'Mga Hugis sa Paligid',sec2sub:'I-tap ang bagay!',sec3:'Paano Iguhit',sec3sub:'I-tap at panoorin!',sec4:'I-trace ang Hugis!',sec4sub:'Sundan ang tuldok!',sec5:'Shape Quiz!',sec5sub:'Anong hugis ang bagay na ito?',sec6:'Hanapin ang Hugis!',sec6sub:'I-tap ang tamang bagay!',sec7:'Kantahin ang Shapes Song!',sec7sub:'Panoorin at kumanta \u2014 i-tap ang video!',closeBtn:'OK!',speak:'Sabihin!',tap:'I-tap!',tapDraw:'Panoorin!',watch:'Panoorin!',replay:'Ulitin',hint:'Pindutin ang Panoorin!',traceHint:'Sundan ang tuldok-tuldok!',traceClear:'Burahin',traceCheck:'Suriin!',traceTap:'I-tap para i-trace!',quizQ:'Anong hugis ito?',quizNext:'Susunod',quizRestart:'Maglaro Ulit!',quizDoneTitle:'Tapos na!',findFound:'Nahanap',findTotal:'Lahat',findMissionLabel:'Hanapin ang lahat ng...',findMissionSub:'I-tap sila sa playground!',findNextRound:'Susunod na Round',findDoneTitle:'\uD83C\uDF89 Lahat Nahanap!',findDoneSub:'Kahanga-hanga! Pindutin ang Susunod na Round.',letsPlay:'Maglaro na tayo!'},
};

let lang='en', curShape='';
let dmIdx=-1, dmStep=0, dmAnimating=false;
let activeVideoIdx=0;

/* ══ LANGUAGE ══ */
function setLang(l){
    if(l===lang)return; lang=l;
    document.getElementById('btnEN').classList.toggle('active',l==='en');
    document.getElementById('btnTL').classList.toggle('active',l==='tl');
    const u=UI[l];
    document.getElementById('pageTitle').textContent=u.title;
    document.getElementById('langBadgeFlag').textContent=u.badgeFlag;
    document.getElementById('langBadgeText').textContent=u.badgeText;
    document.getElementById('langBadge').classList.toggle('tl-mode',l==='tl');
    ['sec1label','sec1sub','sec2label','sec2sub','sec3label','sec3sub','sec4label','sec4sub',
     'sec5label','sec5sub','sec6label','sec6sub','sec7label','sec7sub','lmClose','speakLabel',
     'tmSub','tmGuideLabel','tmClearLabel','tmCheckLabel','quizQLabel','quizNextLabel',
     'quizRestartLabel','quizDoneTitle','findFoundLabel','findTotalLabel','findMissionLabel',
     'findMissionSub','findNextLabel','findDoneTitle','findDoneSub','letsPlayLabel'
    ].forEach(id=>{
        const map={sec1label:'sec1',sec1sub:'sec1sub',sec2label:'sec2',sec2sub:'sec2sub',sec3label:'sec3',sec3sub:'sec3sub',sec4label:'sec4',sec4sub:'sec4sub',sec5label:'sec5',sec5sub:'sec5sub',sec6label:'sec6',sec6sub:'sec6sub',sec7label:'sec7',sec7sub:'sec7sub',lmClose:'closeBtn',speakLabel:'speak',tmSub:'traceHint',tmGuideLabel:'traceHint',tmClearLabel:'traceClear',tmCheckLabel:'traceCheck',quizQLabel:'quizQ',quizNextLabel:'quizNext',quizRestartLabel:'quizRestart',quizDoneTitle:'quizDoneTitle',findFoundLabel:'findFound',findTotalLabel:'findTotal',findMissionLabel:'findMissionLabel',findMissionSub:'findMissionSub',findNextLabel:'findNextRound',findDoneTitle:'findDoneTitle',findDoneSub:'findDoneSub',letsPlayLabel:'letsPlay'};
        const key=map[id]||id;
        if(u[key]!==undefined)document.getElementById(id).textContent=u[key];
    });
    document.querySelectorAll('.rl-sname').forEach(el=>el.textContent=el.dataset[l]);
    document.querySelectorAll('.rl-stap').forEach(el=>el.textContent=u.tap);
    document.querySelectorAll('.rl-item-label').forEach(el=>el.textContent=el.dataset[l]);
    document.querySelectorAll('.draw-cname').forEach(el=>el.textContent=el.dataset[l]);
    document.querySelectorAll('[id^="dcardtip-"]').forEach(el=>el.textContent=u.tapDraw);
    document.querySelectorAll('.trace-card-hint').forEach(el=>el.textContent=u.traceTap);
    renderGrid(l);
    renderTraceGrid(l);
    quizRenderQuestion();
    findUpdateMissionText();
    renderVideoTabs(l);
    closeLM();
}

/* ══ SECTION 1: LEARN GRID ══ */
function renderGrid(l){
    const g=document.getElementById('shapesGrid');
    g.style.opacity='0';g.style.transform='scale(.96)';g.style.transition='opacity .25s,transform .25s';
    setTimeout(()=>{
        g.innerHTML='';
        const src=l==='en'?SHAPES_EN:SHAPES_TL;
        src.forEach((s,i)=>{
            const m=SHAPE_META[i];
            const other=l==='en'?SHAPES_TL[i]:SHAPES_EN[i];
            const c=document.createElement('div');
            c.className='shape-card';
            c.style.cssText=`--cc:${m.color};animation-delay:${(i*.07).toFixed(2)}s`;
            c.onclick=()=>openLM(i,l);
            c.innerHTML=`<div class="s-svg"><svg viewBox="0 0 100 100"><path d="${m.path}" fill="${m.color}"/></svg></div><div class="s-name">${s[0]}</div><div class="s-local">(${other[0]})</div><div class="s-desc">${s[1]}</div>`;
            g.appendChild(c);
        });
        g.style.opacity='1';g.style.transform='scale(1)';
    },180);
}
function openLM(i,l){
    const m=SHAPE_META[i];
    const cur=(l==='en'?SHAPES_EN:SHAPES_TL)[i];
    const other=(l==='en'?SHAPES_TL:SHAPES_EN)[i];
    curShape=cur[0];
    document.getElementById('lmPath').setAttribute('d',m.path);
    document.getElementById('lmPath').setAttribute('fill',m.color);
    document.getElementById('lmName').textContent=cur[0];
    document.getElementById('lmName').style.color=m.color;
    document.getElementById('lmLocal').textContent=`(${other[0]})`;
    document.getElementById('lmDesc').textContent=cur[1];
    document.getElementById('learnModal').style.setProperty('--mc',m.color);
    document.getElementById('speakLabel').textContent=UI[lang].speak;
    document.getElementById('lmClose').textContent=UI[lang].closeBtn;
    const sv=document.querySelector('.lm-svg');
    sv.style.animation='none';sv.offsetHeight;sv.style.animation='';
    document.getElementById('learnOverlay').classList.add('active');
    confetti(m.color);
    setTimeout(speakIt,350);
}
function closeLM(){document.getElementById('learnOverlay').classList.remove('active');window.speechSynthesis.cancel();document.getElementById('speakBtn').classList.remove('speaking');}
function speakIt(){if(!curShape)return;window.speechSynthesis.cancel();speakWord(curShape,lang);}
function speakWord(word,l){
    window.speechSynthesis.cancel();
    const b=document.getElementById('speakBtn');b.classList.add('speaking');
    if(l==='tl'){
        const u=new SpeechSynthesisUtterance(word);u.lang='fil-PH';u.rate=0.72;u.pitch=1.1;u.volume=1;
        const voices=window.speechSynthesis.getVoices();
        const fv=voices.find(v=>v.lang==='fil-PH'||v.lang==='fil'||v.name.toLowerCase().includes('filipino')||v.name.toLowerCase().includes('tagalog'));
        if(fv)u.voice=fv;else{u.lang='en-US';u.rate=0.65;}
        const rt=setInterval(()=>{if(window.speechSynthesis.paused)window.speechSynthesis.resume();},200);
        u.onend=()=>{clearInterval(rt);b.classList.remove('speaking');};
        u.onerror=()=>{clearInterval(rt);b.classList.remove('speaking');};
        window.speechSynthesis.speak(u);
    } else {
        const u=new SpeechSynthesisUtterance(word);u.lang='en-US';u.rate=0.85;u.pitch=1.2;
        u.onend=()=>b.classList.remove('speaking');u.onerror=()=>b.classList.remove('speaking');
        window.speechSynthesis.speak(u);
    }
}

/* ══ SECTION 2: REAL LIFE ══ */
function openRL(el){
    const name=lang==='en'?el.dataset.enName:el.dataset.tlName;
    const fact=lang==='en'?el.dataset.enFact:el.dataset.tlFact;
    const shape=lang==='en'?el.dataset.shapeEn:el.dataset.shapeTl;
    const color=el.dataset.color, path=el.dataset.path;
    const imgUrl=el.dataset.img, svgContent=el.dataset.svg;

    const modalImg=document.getElementById('rlModalImg');
    modalImg.classList.remove('broken');
    modalImg.src=imgUrl;
    modalImg.alt=name;
    document.getElementById('rlFallbackSvg').innerHTML=svgContent;

    document.getElementById('rlName').textContent=name;
    document.getElementById('rlFact').textContent=fact;
    document.getElementById('rlTagLabel').textContent=shape;
    const tp=document.getElementById('rlTagPath');
    tp.setAttribute('d',path);tp.setAttribute('fill',color);
    document.getElementById('rlOverlay').classList.add('active');
    confetti(color);
}
function closeRL(){document.getElementById('rlOverlay').classList.remove('active');}

/* ══ SECTION 3: DRAW MODAL ══ */
function openDrawModal(idx){
    dmIdx=idx;dmStep=0;dmAnimating=false;
    const d=DRAW_DATA[idx],u=UI[lang];
    document.getElementById('dmModal').style.setProperty('--stroke-color',d.color);
    document.getElementById('dmPreviewPath').setAttribute('d',d.path);
    document.getElementById('dmPreviewPath').setAttribute('fill',d.color);
    document.getElementById('dmTitle').textContent=lang==='en'?d.name:TL_NAMES[idx];
    document.getElementById('dmTip').textContent=lang==='en'?d.tipEN:d.tipTL;
    document.getElementById('dmGhost').setAttribute('d',d.path);
    const grp=document.getElementById('dmStrokesGroup');grp.innerHTML='';
    d.strokes.forEach((s,i)=>{const p=document.createElementNS('http://www.w3.org/2000/svg','path');p.setAttribute('class','stroke-path');p.setAttribute('d',s);p.id=`dsp-${idx}-${i}`;grp.appendChild(p);});
    const dots=document.getElementById('dmDots');dots.innerHTML='';
    d.strokes.forEach((_,i)=>{const dot=document.createElement('div');dot.className='step-dot';dot.id=`dot-${idx}-${i}`;dots.appendChild(dot);});
    document.getElementById('dmFooterHint').textContent=u.hint;
    document.getElementById('dmWatchLabel').textContent=u.watch;
    document.getElementById('dmReplayLabel').textContent=u.replay;
    document.getElementById('dmNextBtn').disabled=false;
    document.getElementById('dmOverlay').classList.add('active');
}
function closeDM(){
    document.getElementById('dmOverlay').classList.remove('active');dmAnimating=false;
    if(dmIdx>=0)DRAW_DATA[dmIdx].strokes.forEach((_,i)=>{const p=document.getElementById(`dsp-${dmIdx}-${i}`);if(p)p.classList.remove('done','active');});
}
function replayDM(){
    if(dmAnimating)return;dmStep=0;
    DRAW_DATA[dmIdx].strokes.forEach((_,i)=>{const p=document.getElementById(`dsp-${dmIdx}-${i}`);const dot=document.getElementById(`dot-${dmIdx}-${i}`);if(p)p.classList.remove('done','active');if(dot)dot.classList.remove('done','active');});
    document.getElementById('dmNextBtn').disabled=false;
    document.getElementById('dmFooterHint').textContent=UI[lang].hint;
    playAllStrokes();
}
function playAllStrokes(){
    if(dmIdx<0)return;dmAnimating=true;document.getElementById('dmNextBtn').disabled=true;
    const total=DRAW_DATA[dmIdx].strokes.length;
    function playOne(step){
        if(step>=total){dmAnimating=false;dmStep=total;document.getElementById('dmNextBtn').disabled=true;document.getElementById('dmFooterHint').textContent=lang==='en'?'Great job!':'Magaling!';confetti(DRAW_DATA[dmIdx].color);return;}
        const p=document.getElementById(`dsp-${dmIdx}-${step}`),dot=document.getElementById(`dot-${dmIdx}-${step}`);
        if(p){p.classList.remove('done');p.classList.add('active');}
        if(dot){dot.classList.remove('done');dot.classList.add('active');}
        setTimeout(()=>{if(p)p.classList.add('done');if(dot){dot.classList.remove('active');dot.classList.add('done');}setTimeout(()=>playOne(step+1),180);},750);
    }
    playOne(0);
}
function nextStrokeDM(){if(!dmAnimating)playAllStrokes();}

/* ══ SECTION 4: SHAPE TRACING ══ */
let traceCtx,traceColor,traceShapeIdx=-1,traceDrawing=false,tracePixelsTotal=0;
const TRACE_GUIDES=[
    (ctx,W,H)=>{ctx.beginPath();ctx.arc(W/2,H/2,Math.min(W,H)*.38,0,Math.PI*2);},
    (ctx,W,H)=>{const s=Math.min(W,H)*.7;ctx.rect((W-s)/2,(H-s)/2,s,s);},
    (ctx,W,H)=>{ctx.moveTo(W/2,H*.08);ctx.lineTo(W*.92,H*.88);ctx.lineTo(W*.08,H*.88);ctx.closePath();},
    (ctx,W,H)=>{ctx.rect(W*.05,H*.18,W*.9,H*.64);},
    (ctx,W,H)=>{ctx.ellipse(W/2,H/2,W*.42,H*.32,0,0,Math.PI*2);},
    (ctx,W,H)=>{const cx=W/2,cy=H/2,r1=Math.min(W,H)*.44,r2=Math.min(W,H)*.2,pts=5;for(let i=0;i<pts*2;i++){const r=i%2===0?r1:r2,a=(i*Math.PI/pts)-Math.PI/2;i===0?ctx.moveTo(cx+r*Math.cos(a),cy+r*Math.sin(a)):ctx.lineTo(cx+r*Math.cos(a),cy+r*Math.sin(a));}ctx.closePath();},
    (ctx,W,H)=>{const cx=W/2,cy=H/2;ctx.moveTo(cx,cy+H*.28);ctx.bezierCurveTo(cx-W*.42,cy+H*.04,cx-W*.42,cy-H*.28,cx,cy-H*.18);ctx.bezierCurveTo(cx+W*.42,cy-H*.28,cx+W*.42,cy+H*.04,cx,cy+H*.28);},
    (ctx,W,H)=>{ctx.moveTo(W/2,H*.06);ctx.lineTo(W*.92,H/2);ctx.lineTo(W/2,H*.94);ctx.lineTo(W*.08,H/2);ctx.closePath();},
];
function renderTraceGrid(l){
    const g=document.getElementById('traceGrid');g.innerHTML='';
    SHAPE_META.forEach((m,i)=>{
        const name=l==='en'?SHAPES_EN[i][0]:SHAPES_TL[i][0];
        const c=document.createElement('div');c.className='trace-card';c.style.cssText=`--tc:${m.color}`;c.onclick=()=>openTM(i);
        c.innerHTML=`<div class="trace-card-svg"><svg viewBox="0 0 100 100"><path d="${m.path}" fill="${m.color}" opacity=".13"/><path d="${m.path}" fill="none" stroke="${m.color}" stroke-width="5" stroke-dasharray="8 5" stroke-linecap="round" stroke-linejoin="round"/></svg></div><div class="trace-card-name">${name}</div><div class="trace-card-hint">${UI[l].traceTap}</div><div class="trace-badge"><i class="fas fa-pen"></i></div>`;
        g.appendChild(c);
    });
}
function openTM(idx){
    traceShapeIdx=idx;const m=SHAPE_META[idx];
    const name=lang==='en'?SHAPES_EN[idx][0]:SHAPES_TL[idx][0];
    traceColor=m.color;
    document.getElementById('tmPreviewPath').setAttribute('d',m.path);
    document.getElementById('tmPreviewPath').setAttribute('fill',m.color);
    document.getElementById('tmTitle').textContent=name;
    document.getElementById('tmSub').textContent=UI[lang].traceHint;
    document.getElementById('tmCheckBtn').style.setProperty('--trace-color',m.color);
    document.getElementById('tmCoverageNum').textContent='0';
    document.getElementById('tmOverlay').classList.add('active');
    setTimeout(()=>initTraceCanvas(idx),50);
}
function initTraceCanvas(idx){
    const canvas=document.getElementById('traceCanvas');traceCtx=canvas.getContext('2d');
    const W=canvas.width,H=canvas.height;traceCtx.clearRect(0,0,W,H);
    traceCtx.save();traceCtx.setLineDash([10,8]);traceCtx.strokeStyle=traceColor;traceCtx.lineWidth=6;traceCtx.globalAlpha=.28;
    traceCtx.beginPath();TRACE_GUIDES[idx](traceCtx,W,H);traceCtx.stroke();traceCtx.restore();
    const off=document.createElement('canvas');off.width=W;off.height=H;const offCtx=off.getContext('2d');
    offCtx.beginPath();TRACE_GUIDES[idx](offCtx,W,H);offCtx.lineWidth=28;offCtx.strokeStyle='#000';offCtx.stroke();
    const ref=offCtx.getImageData(0,0,W,H).data;tracePixelsTotal=0;
    for(let i=3;i<ref.length;i+=4)if(ref[i]>10)tracePixelsTotal++;
    traceDrawing=false;canvas.onpointerdown=startTrace;canvas.onpointermove=moveTrace;canvas.onpointerup=()=>{traceDrawing=false;};
}
function startTrace(e){e.preventDefault();traceDrawing=true;const{x,y}=getPos(e);traceCtx.beginPath();traceCtx.moveTo(x,y);traceCtx.strokeStyle=traceColor;traceCtx.lineWidth=24;traceCtx.lineCap='round';traceCtx.lineJoin='round';traceCtx.globalAlpha=.72;}
function moveTrace(e){if(!traceDrawing)return;e.preventDefault();const{x,y}=getPos(e);traceCtx.lineTo(x,y);traceCtx.stroke();traceCtx.beginPath();traceCtx.moveTo(x,y);updateCoverage();}
function getPos(e){const canvas=document.getElementById('traceCanvas');const rect=canvas.getBoundingClientRect();const sx=canvas.width/rect.width,sy=canvas.height/rect.height;const src=e.touches?e.touches[0]:e;return{x:(src.clientX-rect.left)*sx,y:(src.clientY-rect.top)*sy};}
function updateCoverage(){if(!traceCtx||tracePixelsTotal===0)return;const W=document.getElementById('traceCanvas').width,H=document.getElementById('traceCanvas').height;const off=document.createElement('canvas');off.width=W;off.height=H;const offCtx=off.getContext('2d');offCtx.beginPath();TRACE_GUIDES[traceShapeIdx](offCtx,W,H);offCtx.lineWidth=28;offCtx.strokeStyle='#000';offCtx.stroke();const ref=offCtx.getImageData(0,0,W,H).data,user=traceCtx.getImageData(0,0,W,H).data;let covered=0;for(let i=0;i<ref.length;i+=4)if(ref[i+3]>10&&user[i+3]>30)covered++;document.getElementById('tmCoverageNum').textContent=Math.min(100,Math.round(covered/tracePixelsTotal*100));}
function clearTrace(){initTraceCanvas(traceShapeIdx);document.getElementById('tmCoverageNum').textContent='0';}
function checkTrace(){const pct=parseInt(document.getElementById('tmCoverageNum').textContent);if(pct>=55){playCorrectSound();flashScreen('green');confetti(traceColor);}else{playWrongSound();flashScreen('red');}}
function closeTM(){document.getElementById('tmOverlay').classList.remove('active');const canvas=document.getElementById('traceCanvas');canvas.onpointerdown=null;canvas.onpointermove=null;canvas.onpointerup=null;}

/* ══ SECTION 5: SHAPE QUIZ ══ */
let quizOrder=[],quizQIdx=0,quizAnswered=false;
function quizShuffle(arr){const a=[...arr];for(let i=a.length-1;i>0;i--){const j=Math.floor(Math.random()*(i+1));[a[i],a[j]]=[a[j],a[i]];}return a;}
function quizInit(){quizOrder=quizShuffle([0,1,2,3,4,5,6,7]);quizQIdx=0;quizAnswered=false;document.getElementById('quizMainArea').style.display='';document.getElementById('quizDone').style.display='none';quizRenderQuestion();}
function quizRenderQuestion(){
    if(quizQIdx>=quizOrder.length){quizShowDone();return;}
    quizAnswered=false;
    const item=QUIZ_ITEMS[quizOrder[quizQIdx]],csi=item[3];
    document.getElementById('quizQLabel').textContent=UI[lang].quizQ;
    document.getElementById('quizNextBtn').disabled=true;
    document.getElementById('quizNextLabel').textContent=UI[lang].quizNext;
    document.getElementById('quizObjectDisplay').innerHTML=`<svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">${item[0]}</svg>`;
    document.getElementById('quizObjectLabel').textContent=lang==='en'?item[1]:item[2];
    const others=quizShuffle([0,1,2,3,4,5,6,7].filter(i=>i!==csi)).slice(0,3);
    const choices=quizShuffle([csi,...others]);
    const grid=document.getElementById('quizChoices');grid.innerHTML='';
    choices.forEach(ci=>{
        const cm=SHAPE_META[ci],cname=lang==='en'?SHAPES_EN[ci][0]:SHAPES_TL[ci][0];
        const div=document.createElement('div');div.className='quiz-choice';
        div.innerHTML=`<div class="quiz-choice-svg"><svg viewBox="0 0 100 100"><path d="${cm.path}" fill="${cm.color}"/></svg></div><div class="quiz-choice-name">${cname}</div>`;
        div.onclick=()=>quizAnswer(ci===csi,div,csi);grid.appendChild(div);
    });
}
function quizAnswer(correct,el,csi){
    if(quizAnswered)return;quizAnswered=true;
    if(correct){el.classList.add('correct');playCorrectSound();flashScreen('green');confetti(SHAPE_META[csi].color);}
    else{el.classList.add('wrong');playWrongSound();flashScreen('red');const cn=lang==='en'?SHAPES_EN[csi][0]:SHAPES_TL[csi][0];document.querySelectorAll('.quiz-choice').forEach(c=>{if(c.querySelector('.quiz-choice-name').textContent===cn)c.classList.add('correct');});}
    document.getElementById('quizNextBtn').disabled=false;
}
function quizNext(){quizQIdx++;quizRenderQuestion();}
function quizShowDone(){document.getElementById('quizMainArea').style.display='none';document.getElementById('quizDone').style.display='block';document.getElementById('quizDoneTitle').textContent=UI[lang].quizDoneTitle;document.getElementById('quizDoneSub').textContent=lang==='en'?'Amazing! You know all 8 shapes!':'Kahanga-hanga! Alam mo na ang 8 hugis!';document.getElementById('quizRestartLabel').textContent=UI[lang].quizRestart;confetti('#FDCB6E');}
function quizRestart(){quizInit();}

/* ══ SECTION 6: FIND THE SHAPE ══ */
let findRoundIdx=0,findFoundCount=0;
function buildClouds(){const scene=document.getElementById('playgroundScene');scene.querySelectorAll('.pg-cloud').forEach(c=>c.remove());[{x:4,y:4,w:100},{x:36,y:2,w:82},{x:70,y:6,w:92}].forEach(c=>{const d=document.createElement('div');d.className='pg-cloud';d.style.cssText=`left:${c.x}%;top:${c.y}%;width:${c.w}px;pointer-events:none;`;d.innerHTML=`<svg viewBox="0 0 110 50" width="${c.w}" height="${Math.round(c.w*50/110)}"><ellipse cx="55" cy="38" rx="48" ry="18" fill="rgba(255,255,255,.78)"/><ellipse cx="38" cy="30" rx="28" ry="22" fill="rgba(255,255,255,.78)"/><ellipse cx="70" cy="28" rx="24" ry="18" fill="rgba(255,255,255,.78)"/></svg>`;scene.insertBefore(d,scene.querySelector('.pg-ground'));});}
function findRenderScene(){
    const r=FIND_ROUNDS[findRoundIdx%FIND_ROUNDS.length];findFoundCount=0;
    document.getElementById('findFound').textContent=0;document.getElementById('findTotal').textContent=r.findCount;document.getElementById('findDoneBanner').classList.remove('show');
    const name=lang==='en'?r.nameEN:r.nameTL;
    document.getElementById('findMissionLabel').textContent=UI[lang].findMissionLabel;
    document.getElementById('findMissionName').textContent=name;
    document.getElementById('findMissionSub').textContent=UI[lang].findMissionSub;
    document.getElementById('findMissionSvg').innerHTML=`<svg viewBox="0 0 100 100" width="56" height="56"><path d="${r.path}" fill="${r.color}"/></svg>`;
    const scene=document.getElementById('playgroundScene');scene.querySelectorAll('.pg-obj').forEach(o=>o.remove());buildClouds();
    const objs=[...r.objects];for(let i=objs.length-1;i>0;i--){const j=Math.floor(Math.random()*(i+1));[objs[i],objs[j]]=[objs[j],objs[i]];}
    objs.forEach(obj=>{const el=document.createElement('div');el.className='pg-obj';el.style.cssText=`left:${obj.x}%;top:${obj.y}%;width:${obj.size}px;height:${obj.size}px;`;el.innerHTML=`<svg viewBox="0 0 100 100" width="${obj.size}" height="${obj.size}" xmlns="http://www.w3.org/2000/svg">${obj.svg}</svg>`;el.dataset.target=obj.isTarget?'1':'0';el.title=lang==='en'?obj.nameEN:obj.nameTL;el.onclick=()=>findTapObj(el,r,obj);scene.appendChild(el);});
    const row=document.getElementById('findChipsRow');row.innerHTML='';
    for(let i=0;i<r.findCount;i++){const chip=document.createElement('div');chip.className='find-chip';chip.id=`fchip-${i}`;chip.innerHTML=`<svg viewBox="0 0 100 100" width="20" height="20"><path d="${r.path}" fill="${r.color}"/></svg> ?`;row.appendChild(chip);}
}
function findTapObj(el,r,obj){
    if(el.classList.contains('found'))return;
    if(el.dataset.target==='1'){
        el.classList.add('found');el.onclick=null;playCorrectSound();flashScreen('green');confetti(r.color);
        const chip=document.getElementById(`fchip-${findFoundCount}`);
        if(chip){chip.classList.add('found-chip');chip.innerHTML=`<svg viewBox="0 0 100 100" width="20" height="20"><path d="${r.path}" fill="${r.color}"/></svg> ✓`;}
        findFoundCount++;document.getElementById('findFound').textContent=findFoundCount;
        if(findFoundCount>=r.findCount)setTimeout(()=>{document.getElementById('findDoneBanner').classList.add('show');confetti('#FDCB6E');},500);
    } else {
        playWrongSound();flashScreen('red');el.classList.add('pg-obj','wrong-shake');
        const x=document.createElement('div');x.className='wrong-x-pop';x.textContent='✕';el.appendChild(x);
        setTimeout(()=>{el.classList.remove('wrong-shake');if(x.parentNode)x.parentNode.removeChild(x);},750);
    }
}
function findNextRound(){findRoundIdx++;findRenderScene();}
function findUpdateMissionText(){const r=FIND_ROUNDS[findRoundIdx%FIND_ROUNDS.length];const name=lang==='en'?r.nameEN:r.nameTL;document.getElementById('findMissionLabel').textContent=UI[lang].findMissionLabel;document.getElementById('findMissionName').textContent=name;document.getElementById('findMissionSub').textContent=UI[lang].findMissionSub;}

/* ══ SECTION 7: VIDEOS ══ */
function renderVideoTabs(l){const vids=VIDEOS[l]||VIDEOS.en;const tabs=document.getElementById('videoTabs');tabs.innerHTML='';vids.forEach((v,i)=>{const btn=document.createElement('button');btn.className='vid-tab'+(i===activeVideoIdx?' active':'');btn.innerHTML=`<i class="fas ${v.icon}"></i> ${l==='en'?v.labelEN:v.labelTL}`;btn.onclick=()=>selectVideo(i,l);tabs.appendChild(btn);});loadVideo(vids[activeVideoIdx].id);}
function selectVideo(idx,l){activeVideoIdx=idx;const vids=VIDEOS[l||lang]||VIDEOS.en;document.querySelectorAll('.vid-tab').forEach((t,i)=>t.classList.toggle('active',i===idx));loadVideo(vids[idx].id);}
function loadVideo(id){document.getElementById('videoIframe').src=`https://www.youtube.com/embed/${id}?rel=0&modestbranding=1`;}

/* ══ CONFETTI ══ */
function confetti(color){const cols=[color,'#FFE66D','#FF6B6B','#A29BFE','#4ECDC4','#FD79A8'];for(let i=0;i<32;i++){const p=document.createElement('div');p.className='cp';p.style.cssText=`left:${Math.random()*100}vw;top:-10px;background:${cols[Math.floor(Math.random()*cols.length)]};border-radius:${Math.random()>.5?'50%':'2px'};width:${6+Math.random()*7}px;height:${6+Math.random()*7}px;animation-duration:${1.2+Math.random()*1.3}s;animation-delay:${Math.random()*.4}s;`;document.body.appendChild(p);p.addEventListener('animationend',()=>p.remove());}}

document.addEventListener('keydown',e=>{if(e.key==='Escape'){closeLM();closeRL();closeDM();closeTM();}});
if(window.speechSynthesis){window.speechSynthesis.getVoices();window.speechSynthesis.onvoiceschanged=()=>window.speechSynthesis.getVoices();}

/* ══ INIT ══ */
renderGrid('en');
renderTraceGrid('en');
quizInit();
findRenderScene();
renderVideoTabs('en');
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