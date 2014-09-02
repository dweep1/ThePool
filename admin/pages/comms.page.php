<div class="fluid-row" ng-controller="ExampleController">

    <div class="fluid-row width-40">

        <h3>Communications</h3>

        <br/>

        <form action="listn.email.php" id="subForm" method="post">
            <div class="fluid-row slim">
                <input ng-model="subject" ng-init="subject = 'Subject'" type="text"  id="subject" name="subject" value="" />
            </div>

            <div class="fluid-row slim">
                <textarea ng-model="message" ng-init="message = '<br/>Enter A Message To Send to Recipients'" style="display:none;" name="message" id="message"></textarea>
                <textarea ng-model="message" id="redactor"></textarea>
            </div>


            <div class="fluid-row slim">
                <select id="email-group" name="email-group">
                    <option value="0">Select Recipients</option>
                    <option value="-1">Testing</option>
                    <option value="1">All Current Users</option>
                    <option value="2">Last Years Users</option>
                </select>
            </div>

            <button class="ui-buttons dark">Send Email</button>

        </form>


    </div>

    <div class="fluid-row width-60">

        <h5>Email Preview</h5>

        <br/>

        <style>
            @import url(http://fonts.googleapis.com/css?family=Open+Sans);

            .email-template h1, .email-template h2, .email-template h3, .email-template h4, .email-template h5{
                font-family: 'Open Sans', sans-serif;
                font-weight:300;
                color:rgb(80,80,80);
                padding:3px 0px;
                margin-right:3px;
                border-bottom:1px solid rgba(120,120,255, 0.9);
            }


        </style>

        <div class="email-template fluid-row slim aligncenter">

            <div style="background: #fff; width:auto; height:100%; padding:10px 20px; text-align: left;">
                <table cellpadding="0" cellspacing="0" style="width:800px; height:auto; margin:10px auto; border:1px solid #b9b9ba; background: #f1f1f2; padding:0px; border-radius:4px; border-bottom: 2px solid rgba(100,100,100,0.9);">
                    <tr>
                        <td style="background: url('http://i.imgur.com/4S4yqzW.png'); border-radius:3px 3px 0px 0px; height:100px;">
                            <img style="border-radius:3px 3px 0px 0px;" src="http://i.imgur.com/4S4yqzW.png">
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:0px 25px; padding-top:15px; font-family: 'Open Sans', sans-serif; font-size: 12px; line-height:22px; color:rgb(80,80,80);">
                            <h3>{{ subject }}</h3>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:0px 25px; padding-bottom:15px; font-family: 'Open Sans', sans-serif; font-size: 12px; line-height:22px; color:rgb(80,80,80);">

                            <div ng-bind-html="deliberatelyTrustDangerousSnippet()"></div>

                        </td>
                    </tr>
                    <tr>
                        <td style="padding:5px 25px; font-family: 'Open Sans', sans-serif; font-size: 10px; line-height:18px; text-align:center; border-top:1px solid #b9b9ba; background: #eaeaeb;">
                            Copyright 'The Pool', Anthony Harris,  2014
                        </td>
                    </tr>
                </table>
            </div>

        </div>


    </div>
</div>

<script>

    $('#redactor').redactor();

    $('[contenteditable]').bind('blur keyup paste copy cut mouseup', function () {

        var scope = angular.element($("#message")).scope();
        scope.$apply(function(){
            scope.message = $('#redactor').val();
        });

        console.log(scope.message);
    });

    function ExampleController($scope, $sce) {

        $scope.message = "<br/>Enter A Message To Send to Recipients";

        $scope.deliberatelyTrustDangerousSnippet = function() {

            return $sce.trustAsHtml($scope.message);
        };

    }

</script>