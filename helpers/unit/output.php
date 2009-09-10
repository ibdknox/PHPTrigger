<html>
<head>
    <title>Test Runner</title>
    <?php echo $this->partial("script", array(), "helpers/unit/"); ?>
    <?php echo $this->partial("style", array(), "helpers/unit/"); ?>
</head>

<body>
    <div class="outerwrapper">
        <a href="#" class="failToggle onlyFailed">Failed tests only</a>
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
