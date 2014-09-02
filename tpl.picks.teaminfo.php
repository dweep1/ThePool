<?php

    if(!session_id()) {
        include "_header.php";
    }

    $teams = teams::getTeamsList();
    $user = users::returnCurrentUser();

    if((int)$user->favorite_team_id === 0)
        $user->favorite_team_id = 1;

    $favoriteTeam = new teams($user->favorite_team_id);

    if(isset($_GET['team_id']))
        $selected_team = teams::selected((int) $_GET['team_id']);
    else
        $selected_team = teams::selected(null, $favoriteTeam);

    $selected_team->getTeamStats();
    $selected_team->getRecentGames();

?>

<h4 style="padding:2%">Recent Games</h4>

<div class="aligncenter">
	<br/>
    <div class="fluid-row slim">
        <div class="fluid-row slim width-50 alignright">
            <br/>
            <h2><?php echo $teams[$selected_team->id]->city; ?></h2>
            <h4><?php echo $teams[$selected_team->id]->team_name; ?></h4>
        </div>
        <div class="fluid-row slim width-50 alignleft">
            <img src="<?php echo $teams[$selected_team->id]->image_url; ?>" class="team-img" />
        </div>
    </div>

    <div class="fluid-row slim">

        <ul class="ui-games-list">
            <?php

            foreach ($selected_team->recentGames as $game){

                $winner = $game->getWinner();

                if($game->away_team == $winner){
                    $picked_away = "picked";
                    $picked_home = "";
                }else{
                    $picked_away = "";
                    $picked_home = "picked";
                }

                if($winner == $game->away_team){
                    $loss_away = "";
                    $loss_home = "loss";
                }else{
                    $loss_away = "loss";
                    $loss_home = "";
                }

                $gameDate = new DateTime($game->date, Core::getTimezone());
                $gameDate->setTime(0,0,0);

                $game->displayDate = $gameDate->format('D, m/d Y');

                echo "    <li>

                            <div class='team alignleft $picked_away' style='background-image: url(\"{$teams[$game->away_team]->image_url}\")'>

                                <div class='gradient-left $loss_away'>
                                    <h5>{$teams[$game->away_team]->city}</h5>
                                    <h6>{$teams[$game->away_team]->team_name}</h6>
                                </div>

                            </div>

                            <div class='middle'>

                                <i class='$loss_away'>
                                    {$game->away_score}
                                </i>

                                <i>@</i>

                                <i class='$loss_home'>
                                    {$game->home_score}
                                </i>

                            </div>

                            <div class='team alignright float-right $picked_home' style='background-image: url(\"{$teams[$game->home_team]->image_url}\")'>

                                <div class='$loss_home gradient-right'>
                                    <h5>{$teams[$game->home_team]->city}</h5>
                                    <h6>{$teams[$game->home_team]->team_name}</h6>
                                </div>
                            </div>

                            <div class='display-date' style='color:#fff; text-align: left'>{$game->displayDate}</div>

                            <div class='clear-fix'></div>

                        </li>";

            }

            ?>

        </ul>
    </div>
</div>

    <!--
	<div class="barHold">
		<h5 style="top:33px;">
			<?php

			/**

				$losses = $team['games'] - $team['wins'];
				$wins = $team['wins'];

				echo "$wins Wins - $losses Losses";

			?>
		</h5>
		<h5 style="top:84px; font-size:16px;">
			<?php

				$scored = userstat::getTeamScore($team_id);

				$allowed = userstat::getTeamScore($team_id, 1);

				echo "$scored Scored - $allowed Allowed";

			?>
		</h5>
		<div id="side1" class="horBar"></div>
		<div id="side2" class="horBar"></div>
	</div>
	<h5 style="margin-top:-15px; margin-bottom: 5px;">Pick Performance</h5>
	<div class="chartHold">
		<div class="pick-info graph text-dark">
			<div class="con">
				<canvas id="top-pick-correct" width="80" height="80" class="chart"></canvas>
				<div class="chart-title con"><h5><?php echo intval($won*100); //weighted% ?>%</h5></div>
			</div>
			<div class="con">
				<h6 title="How many points this team earns users in relation to how many points people bet each week. A team with a low point gain % hasn't been picked much, or looses a lot.">Point Gains<i class="icon-info-sign"></i></h6>
				<input type="hidden" id="topPickCorrect" value="<?php echo $won; //pick% ?>" />
			</div>
		</div>
		<div class="pick-info graph text-dark" style="margin-left:-5px;">
			<div class="con">
				<canvas id="top-pick-points" width="80" height="80" class="chart"></canvas>
				<div class="chart-title con"><h5><?php echo intval($rate*100); //pick% ?>%</h5></div>
			</div>
			<div class="con">
				<h6 title="The rate at which this team is picked from week to week, when facing another team. The higher the percentage, the more often that team is picked.">Rate<i class="icon-info-sign"></i></h6>
				<input type="hidden" id="topPickPoints" value="<?php echo $rate; //pick% ?>" />
			</div>
		</div>
	</div>
</div>

<script defer>

	$(window).ready(function() {
		pageLoad();
	});

	function barCharts(){
		var chart;

		chart = new Highcharts.Chart({

			chart: {

				renderTo: 'side1',
				type: 'bar',
				width: 220,
				height: 60,
				showAxes: false,
				spacingBottom: 0,
				spacingTop: 0,
				spacingLeft: 3,
				spacingRight: 0,
				animation: false,
				reflow: false
			},

			colors: [
				'#b3b3b3',
				'#8fbfe7'
			],

			credits:{enabled: false},

			exporting: {enabled: false},

			title: {text: ""},

			xAxis: {
				categories: [''],
				labels: {enabled: false},
				title: {enabled: false},
				lineColor: '#fff',
				gridLineWidth: 0
			},

			yAxis: {
				labels: {enabled: false},
				title: {enabled: false},
				gridLineWidth: 0,
				showFirstLabel: false,
				lineColor: '#fff'
			},

			legend: {enabled: false	},

			tooltip: {enabled: false},

			plotOptions: {
				series: {
					stacking: 'percent',
					dataLabels: {
						enabled: false
					},
					animation: false
				}
			},

			series: [{

				name: 'Losses',

				data: [<?php echo $losses*3; ?>]

			}, {

				name: 'Wins',

				data: [<?php echo $wins*3; ?>]

			}]
		});


		chart = new Highcharts.Chart({

			chart: {

				renderTo: 'side2',
				type: 'bar',
				width: 220,
				height: 60,
				showAxes: false,
				spacingBottom: 0,
				spacingTop: 0,
				spacingLeft: 3,
				spacingRight: 0,
				animation: false,
				reflow: false

			},

			colors: [
				'#b3b3b3',
				'#b3c323'
			],

			credits:{enabled: false},

			exporting: {enabled: false},

			title: {text: ""},

			xAxis: {
				categories: [''],
				labels: {enabled: false},
				title: {enabled: false},
				lineColor: '#fff',
				gridLineWidth: 0
			},

			yAxis: {
				labels: {enabled: false},
				title: {enabled: false},
				gridLineWidth: 0,
				showFirstLabel: false,
				lineColor: '#fff'
			},

			legend: {enabled: false	},

			tooltip: {enabled: false},

			plotOptions: {
				series: {
					stacking: 'percent',
					dataLabels: {
						enabled: false
					},
					animation: false
				}
			},

			series: [{

				name: 'Allowed',

				data: [<?php echo $allowed; ?>]

			}, {

				name: 'Scored',

				data: [<?php echo $scored; **/ ?>]

			}]
		});
	}

</script>

-->
