<?php
$starttime = microtime(true);
include("config.php");
$totalrows = mysqli_num_rows($mysqli->query("SELECT id FROM entries"));
function http_request($url){
    $ch = curl_init(); 
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    $output = curl_exec($ch); 
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);  
    return array("data"=>$output,"code"=>$httpcode);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Wordle Solver - Dataset</title>
    <meta charset="utf-8">
    <meta name="description" content="Wordle Solver by UserGhost411">
    <meta name="keywords" content="Wordle Solver, Worl Scramble, Word List">
    <meta name="author" content="UserGhost411">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.15/jquery.mask.min.js"></script>
</head>

<body>

    <div class="container" style="margin-top:40px;">
        <h2>Wordle Solver Database</h2>

        <p>Currently there are around <b><?= number_format($totalrows, 0, "", ".") ?></b> data word lists used to play Wordle, we specifically only collect word data that has a length of 5 characters because in Wordle games there are only 5 characters input.</p>
        <p>our database is not complete, often there is a word that we cannot guess, to overcome this you can input a word with 5 characters in the form below</p>
        <hr>
        <?php
        if (isset($_POST['submit']) && $_POST['submit'] == md5(date("d/mY H:i"))) {
            if (ctype_alpha($_POST['word']) && strlen($_POST['word']) == 5) {
                $word = $_POST['word'];
                $result = $mysqli->query("select word from entries where word='$word'");
                if ($result->num_rows >= 1) {
                    echo '<div class="alert alert-danger alert-dismissible fade show"><button type="button" class="close" data-dismiss="alert">&times;</button>This word already in our wordlist database!</div>';
                } else {
                    try {
                        $arrContextOptions=array(
                            "ssl"=>array(
                                "verify_peer"=>false,
                                "verify_peer_name"=>false,
                            ),
                        );  
                        $retreq = http_request("https://api.dictionaryapi.dev/api/v2/entries/en/$word");
                        $ret = json_decode($retreq['data']);
                        if ($retreq['code']!=200 or isset($ret->title)) {
                            echo '<div class="alert alert-danger alert-dismissible fade show"><button type="button" class="close" data-dismiss="alert">&times;</button>Sorry! we cant validate the word you have sent</div>';
                        } else {
                            $mysqli->query("INSERT INTO entries (word) VALUES ('$word')");
                            echo '<div class="alert alert-primary alert-dismissible fade show"><button type="button" class="close" data-dismiss="alert">&times;</button>Thank you for your Contributions!</div>';
                        }
                    } catch (Exception $e) {
                        echo '<div class="alert alert-danger alert-dismissible fade show"><button type="button" class="close" data-dismiss="alert">&times;</button>Sorry!, Our Validation api is having problems Right Now</div>';
                    }
                }
            } else {
                echo '<div class="alert alert-danger alert-dismissible fade show"><button type="button" class="close" data-dismiss="alert">&times;</button>Invalid Character Found!</div>';
            }
        } ?>
        <form action="" method="POST">
            <div class="form-group">
                <label for="pwd">Input Word:</label>
                <input type="text" class="form-control" maxlength="5" minlength="5" placeholder="Enter the word" name="word" value="" required>
            </div>
            <button type="submit" name="submit" value="<?= md5(date("d/mY H:i")) ?>" class="btn btn-sm btn-primary">Input Data</button>
            <a href="./" class="btn btn-sm btn-outline-danger float-right">Back to Home</a>
        </form>
        <?php
        $endtime = microtime(true);
        ?>
        <hr>
        <div class='row'>
            <div class='col-6'><small><span class="d-none d-sm-inline">Page </span>Generated in <?= round(($endtime - $starttime) * 1000, 3) ?> ms</small></div>
            <div class='col-6'><small class='float-right'><span class="d-none d-sm-inline">Wordle Solver copyright </span>&copy; <a target='_blank' href='https://github.com/UserGhost411/Wordle-Solver'>UserGhost411</a></small></div>
        </div><br><br>
    </div>
</body>

</html>