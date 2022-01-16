var calculateButton = document.querySelector(".calculate_button");

calculateButton.addEventListener("click", () => {
    onCalculateClick();
});

function collect_data(){

    var inputs = document.querySelectorAll(".input_block");
    data = {}

    inputs.forEach(inputs => {
        data[inputs.querySelector("input").name] = inputs.querySelector("input").value;
    });

    return data;
}

function calculate_deposit(){
    const promise = axios.post("/calc.php", collect_data())
    return promise;
}

function onCalculateClick(){
    const promise = calculate_deposit();
    promise.then(onCalculdated)
        .catch(window.alert);
};

function onCalculdated(response){
    document.querySelector(".result h1").innerHTML = response["data"]["sum"];
    console.log("I got response!")
    console.log(response);
};