<html>
<head>
    <title>Test Runner</title>
    <style type="text/css">

        /* Reset browser styles: get to a known state. */
        html, body, h1, h2, h3, h4, h5, h6, table, tr, th, td, form, fieldset, select, input, textarea, dl, dt, dd, ul, ol, li, address, blockquote, pre, code { margin: 0; padding: 0; }
        body { font: normal 62.5% Verdana, sans-serif; }
        h1, h2, h3, h4, h5, h6 { font-size: 100%; }

        a { outline: none; }
        img { border: none; }

        p { margin: 1em 0; }
        * html p { margin: .83333em 0; }

        /* Doc flow. */
        .block { display: block !important; }
        .inline { display: inline !important; }
        .left { float: left !important; display: inline !important; }
        .right { float: right !important; display: inline !important; }
        .clear { clear: both !important; }

        /* Margins. */
        .collapse { margin: 0 !important; }
        .first { margin-left: 0 !important; }
        .last { margin-right: 0 !important; }
        .top { margin-top: 0 !important; }
        .bottom { margin-bottom: 0 !important; }

        body { background: #0c0f12; color:#ccc; font-family: Verdana, Arial, serif; font-size:110%; }
        div.outerwrapper { margin: 0 auto; margin-top:40px; width: 800px; }
        div.test { margin-bottom: 10px; background:#161B21; border: 1px solid #384554; padding:15px; }
        .failed { color: #FF8934; }
        .passed { color: #3FFF6E; }

        span { font-size:80%; }

        ul { list-style:none; margin-left: 25px; margin-top:15px; }
        ul li { margin-bottom:5px; background:#2D3545; color:#ccc; }
        ul.failed li { border: 1px solid #FF8934; padding:10px; background: #451E17; }
        ul.failed li h3 { color: #FF8934; }
        ul.passed li { color: #3FFF6E; border: 1px solid #3FFF6E; padding:10px; background:#2B4531; }

        del { color: #FF8934; }
        ins { color: #3FFF6E; }

    </style>
</head>

<body>
    <div class="outerwrapper">
        <?php foreach( unit::$units as $test => $result ) { ?>
        <div class="test">
            <h2 class="<?php echo unit::passFail($result); ?>">
                <?php echo $test . ' ( '. unit::passFailHeader($result). ' )'; ?>
            </h2>
            <span class="<?php echo unit::passFail($result); ?>">
                Executed in: <?php echo $result->times['total']; ?>
            </span>
            <ul class="failed">
                <?php foreach($result->errors as $failed) { ?>
                <li>
                    <h3><?php echo $failed['function'] . " ( Line $failed[line] )"; ?></h3>
                    <p>
                        <?php echo unit::formatError($failed); ?>
                    </p>
                </li>
                <?php } ?>
            </ul>
            <ul class="passed">
                <?php foreach($result->passedtests as $passed) { ?>
                <li>
                    <h3><?php echo $passed; ?></h3>
                    <span class="">
                        Executed in: <?php echo $result->times[$passed]; ?>
                    </span>
                </li>
                <?php } ?>
            </ul>
        </div>
        <?php } ?>
    </div>
</body>
</html
