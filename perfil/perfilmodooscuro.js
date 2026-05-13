const boton = document.getElementById("modo-oscuro-check");

if(localStorage.getItem("tema") == "oscuro"){

    document.body.classList.add("dark-mode");
    check.checked = true;
}

check.addEventListener("change" , () =>{
    document.body.classList.toggle("dark-mode");
    if(document.body.classList.contains("dark-mode")){
        localStorage.setItem("tema", "oscuro");
    }else{
        document.body.classList.remove("dark-mode")
        localStorage.setItem("tema", "claro");
    }
});