<?php
  function raise_error($message){
    echo "<h1>Errror!<h1>";
    echo "<p>$message<p>";
    http_response_code(400);
    die();
  };

  function validate_request($request){
    $sum = $request["sum"];
    if ($sum < 1000 or  $sum > 3000000){
      raise_error("Sum $sum is invalid");
    };
    $term = $request["term"];
    if ($term < 1 or  $term > 60){
      raise_error("Term $term is invalid");
    };
    $sumAdd = $request["sumAdd"];
    if ($sumAdd < 0 or  $sumAdd > 3000000){
      raise_error("SumAdd $sumAdd is invalid");
    };
    $percent = $request["percent"];
    if ($percent < 0 or  $percent > 3000000){
      raise_error("Percent $percent is invalid");
    };
  };

  function cal_days_in_year($year){
    $days=0; 
    for($month=1; $month<=12; $month++){ 
      $days = $days + cal_days_in_month(CAL_GREGORIAN,$month,$year);
    }
    return $days;
  };

  function calculate_deposit($request){
    // sumN = sumN-1 + (sumN-1 + sumAdd) * daysN * (percent / daysY)

    // sumN – сумма на счете на N месяц (руб)
    // sumN-1 – сумма на счете на конец прошлого месяца
    // sumAdd – сумма ежемесячного пополнения
    // daysN – количество дней в данном месяце, на которые приходился вклад
    // percent – процентная ставка банка
    // daysY – количество дней в году
    $sumAdd = 0;//$request["sumAdd"];  //sumAdd
    $percent = $request["percent"]/100; //percent
    $term = $request["term"];

    $last_month_sum = $request["sum"]; //sumN-1
    $cur_date = getDate(strtotime($request["startDate"]));

    // $settlement_date = date_create("$cur_date['year']-$cur_date['month']-")

    $year = $cur_date['year'];
    $day = $cur_date['day'];
    $days_in_cur_month = cal_days_in_month( //daysN
      CAL_GREGORIAN,
      $cur_date['mon'], 
      $cur_date['year']);
    $days_in_cur_year = cal_days_in_year($cur_date['year']);  //daysY

    // echo "percent: $percent ";
    // echo "days_in_cur_month: $days_in_cur_month ";
    // echo "percdays_in_cur_yearent: $days_in_cur_year ";

    for($counted_months=0; $counted_months < $term; $counted_months++){
      $last_month_sum = $last_month_sum 
        + ($last_month_sum + $sumAdd) 
          * $days_in_cur_month 
          * ($percent / $days_in_cur_year);
      $sumAdd = $request["sumAdd"];
      $days_in_cur_month = cal_days_in_month( //daysN
        CAL_GREGORIAN,
        $cur_date['mon']+1%12+1, 
        $cur_date['year']);
    };

    return [ 'sum' => $last_month_sum, ];
  }


  // $request_json = '{"startDate":"2021-09-04","term":"10","sum":"10000","percent":"10"}';
  // $request = json_decode($request_json, true);

  // print_r($request);

  // json_encode(calculate_deposit($request));
  

  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $request_json = file_get_contents('php://input');
    $request = json_decode($request_json, true);

    validate_request($request);
    
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(calculate_deposit($request));
  };
?>