//HAMBURGHER MENU//
const apriMenu = document.getElementById("apriMenu");
const menu = document.getElementById("menu");
if(apriMenu) {
    apriMenu.addEventListener("click", function(event){
        console.log(menu.classList); // Verifica le classi dell'elemento menu
        console.log(menu.classList.contains("nascosto"));
        menu.classList.contains("nascosto") ? menu.classList.remove("nascosto"): menu.classList.add("nascosto");
    })
}
//Messaggio AR
const Avviso = document.querySelector(".messaggioAR");
if(Avviso) {
    setTimeout(function(){
        Avviso.remove();
    }, 5000)
}

//LOGIN//
const FormUserLogin = document.getElementById("username");
const FormPswLogin = document.getElementById("password");
const FormLogin = document.getElementById("loginForm");

if(FormLogin) {
    FormUserLogin.addEventListener("blur", function(){
        if(!checkUsername(FormUserLogin.value))
            showErrorForm(FormUserLogin, "L'username può contenere da 4 a 20 caratteri alfabetici");
        else removeError(FormUserLogin);
    })
    //PASSWORD VUOTA
    FormPswLogin.addEventListener("blur", function(){
        if(!FormPswLogin.value)
            showErrorForm(FormPswLogin, "Inserire una password");
        else removeError(FormPswLogin);
    })
    FormLogin.addEventListener("submit", function(event){
        if(!checkUsername(FormUserLogin.value)|| !FormPswLogin.value) {
            event.preventDefault();
            alert("Inserire correttamente le credenziali.");
            var erroriForm = document.getElementsByClassName("errorForm");
            (erroriForm[0]).previousElementSibling.focus();
        }
        
    })
}
//SIGNUP//
FormNameSup = document.getElementById("name");
FormSNameSup = document.getElementById("surname");
FormUserSup = document.getElementById("username");
FormEmailSup = document.getElementById("email");
FormPswSup = document.getElementById("password");
FormSup = document.getElementById("signupForm");
if(FormSup) {
    FormNameSup.addEventListener("blur", function(){
        if(!checkName(FormNameSup.value))
            showErrorForm(FormNameSup, "Sono consentiti solo da 3 a 64 caratteri alfabetici per questo campo.")
        else removeError(FormNameSup);
    })
    FormSNameSup.addEventListener("blur", function(){
        if(!checkName(FormSNameSup.value))
            showErrorForm(FormSNameSup, "Sono consentiti solo da 3 a 64 caratteri alfabetici per questo campo.")
        else removeError(FormSNameSup);
    })
    FormUserSup.addEventListener("blur", function(){
        if(!checkUsername(FormUserSup.value))
            showErrorForm(FormUserSup, "Sono consentiti solo da 4 a 20 caratteri alfabetici per questo campo.")
        else removeError(FormUserSup);
    })
    FormEmailSup.addEventListener("blur", function(){
        if(!checkEmail(FormEmailSup.value))
            showErrorForm(FormEmailSup, "Formato indirizzo email non valido.")
        else removeError(FormEmailSup);
    })
    FormPswSup.addEventListener("input", function(){
        if(!checkPassword(FormPswSup.value))
            showErrorForm(FormPswSup, "Sono consentiti da 4 a 63 caratteri per questo campo. Spazi non consentiti")
        else removeError(FormPswSup);
    })
    FormSup.addEventListener("submit", function(event){
        if(!checkName(FormNameSup.value)||!checkName(FormSNameSup.value)||!checkUsername(FormUserSup.value)||!checkEmail(FormEmailSup.value)||!checkPassword(FormPswSup.value)) {
            event.preventDefault();
            alert("Compilare correttamente il form");
            var erroriForm = document.getElementsByClassName("errorForm");
            (erroriForm[0]).previousElementSibling.focus();
        }
    })
}

//ADMIN
const BtnCreaScheda = document.getElementById("creaLink");
const BtnCercaSchede = document.getElementById("schedeLink");
const SecCreaScheda = document.getElementById("creaSchedaWin");
const SecCercaSchede = document.getElementById("searchSchedeWin");
const formCercaSchede = document.getElementById("searchForm")
const SecEliminaUtenti = document.getElementById("gestioneUtentiWin");
const BtnEliminaUtenti = document.getElementById("utentiLink");

if(SecCreaScheda && SecCercaSchede && SecEliminaUtenti) {
    window.onload = () => {
        if (window.location.href.includes('admin') && localStorage.getItem('admin')) {
          var section = localStorage.getItem('admin');
          document.querySelector(`#${section}`).click();
        } else showAdminSection(SecCreaScheda, BtnCreaScheda);
    }   

    BtnCreaScheda.addEventListener("click", function(){
        showAdminSection(SecCreaScheda, BtnCreaScheda);
    })

    BtnCercaSchede.addEventListener("click", function(){
        showAdminSection(SecCercaSchede, BtnCercaSchede);
    })

    BtnEliminaUtenti.addEventListener("click", function(){
        showAdminSection(SecEliminaUtenti, BtnEliminaUtenti);
    })

    formCercaSchede.addEventListener("submit", function() {
        showAdminSection(SecCercaSchede, BtnCercaSchede);
    })

    

    //-Crea scheda
    //Atleta, dataDiInizio, dataDiFine
    const atletaCreaScheda = document.getElementById("Atleta");
    const inizioCreaScheda = document.getElementById("dataDiInizio");
    const fineCreaScheda = document.getElementById("dataDiFine");
    const formCreaScheda = document.getElementById("creaSchedaForm");

    atletaCreaScheda.addEventListener("input", function(){
        if(!checkSelect(atletaCreaScheda))
            showErrorForm(atletaCreaScheda, "Selezionare un atleta");
        else removeError(atletaCreaScheda);
    })

    inizioCreaScheda.addEventListener("input", function(){
        if(!checkData(inizioCreaScheda))
            showErrorForm(inizioCreaScheda, "La data deve essere nel formato \"dd-mm-aaaa\"");
        else removeError(inizioCreaScheda);
    })
    fineCreaScheda.addEventListener("input", function(){
        if(!checkData(fineCreaScheda))
            showErrorForm(fineCreaScheda, "La data deve essere nel formato \"dd-mm-aaaa\"");
        else if (!checkPeriodo(inizioCreaScheda, fineCreaScheda))
            showErrorForm(fineCreaScheda, "La data di fine periodo deve essere la stessa o successiva a quella di inizio");
        else removeError(fineCreaScheda);
    })



    const fieldset =  document.querySelectorAll("fieldset.fieldsetEsercizio");
    var primoEs = fieldset[0].getElementsByClassName('nomeEsercizio')[0];

    fieldset.forEach(function(fieldset){
        var esCreaScheda = fieldset.getElementsByClassName('nomeEsercizio')[0];
        var ripCreaScheda = fieldset.getElementsByClassName('ripetizioniEsercizio')[0];
        var recCreaScheda = fieldset.getElementsByClassName("recuperoEsercizio")[0];
        var noteCreaScheda = fieldset.getElementsByClassName("noteEsercizio")[0];
        var errori = fieldset.getElementsByClassName('errorForm');
    
        ripCreaScheda.disabled = true;
        recCreaScheda.disabled = true;
        noteCreaScheda.disabled = true;

        esCreaScheda.addEventListener("change", function(){
            if(!checkSelect(esCreaScheda)) {
                ripCreaScheda.value = '';
                ripCreaScheda.disabled = true;
                recCreaScheda.value = '';
                recCreaScheda.disabled = true;
                noteCreaScheda.value = '';
                noteCreaScheda.disabled = true;
                Array.from(errori).forEach(errore => errore.remove());
            }
            else {
                ripCreaScheda.disabled = false;
                recCreaScheda.disabled = false;
                noteCreaScheda.disabled = false;
            }
        })

        ripCreaScheda.addEventListener("blur", function(){
            removeError(ripCreaScheda);
            if(ripCreaScheda.disabled == false && !checkRipetizioni(ripCreaScheda.value))
                showErrorForm(ripCreaScheda, "Per questo campo sono consentiti da 3 a 50 caratteri alfanumerici e simboli - () . ; , * ×");
        })

        recCreaScheda.addEventListener("blur", function(){
            removeError(recCreaScheda);
            if(recCreaScheda.disabled == false && !checkRecupero(recCreaScheda.value))
                showErrorForm(recCreaScheda, "Per questo campo sono consentiti da 3 a 50 caratteri alfanumerici e simboli - () . ; , * ×");
        })

        noteCreaScheda.addEventListener("input", function(){
            removeError(noteCreaScheda);
            if(noteCreaScheda.disable == false && !checkRecupero(noteCreaScheda.value))
                showErrorForm(noteCreaScheda, "Per questo campo sono consentiti al massimo 500 caratteri");
        })
    })
    formCreaScheda.addEventListener("submit", function(event){
        var erroriForm = document.getElementsByClassName("errorForm");
        var tipologiaCreaScheda = Array.from(document.getElementsByClassName('nomeEsercizio')).filter(esercizio => esercizio.value!= "");
        var ripCreaScheda = Array.from(document.getElementsByClassName('ripetizioniEsercizio')).filter(ripetizione => !ripetizione.disabled);
        var recCreaScheda = Array.from(document.getElementsByClassName('recuperoEsercizio')).filter(recupero => !recupero.disabled);
        var noteCreaScheda = Array.from(document.getElementsByClassName('noteEsercizio')).filter(nota => !nota.disabled);

        if(!checkSelect(atletaCreaScheda) ||!checkData(inizioCreaScheda) || !checkData(fineCreaScheda) || !checkPeriodo(inizioCreaScheda, fineCreaScheda)) {
            event.preventDefault();
            alert("Compilare correttamente l'intestazione");
            erroriForm[0] ? (erroriForm[0]).previousElementSibling.focus(): atletaCreaScheda.focus();
        }
        else if(!ripCreaScheda.every(ripetizione => checkRipetizioni(ripetizione.value)) ||
                !recCreaScheda.every(recupero => checkRecupero(recupero.value)) ||
                !noteCreaScheda.every(nota => checkNoteScheda(nota.value))) {
            event.preventDefault();
            alert("Ricontrollare i campi form");
            erroriForm[0] ?(erroriForm[0]).previousElementSibling.focus(): primoEs.focus();
        }
        else if(tipologiaCreaScheda.length==0) {
            event.preventDefault();
            alert("Inserire almeno un esercizio.");
            primoEs.focus();
        }
    })
}

//SCHEDA ADMIN//
const BtnSchedaAdmin = document.getElementById("visualizzaBtn");
const SecSchedaAdmin = document.getElementById("schedaWin");
const BtnModPerAdmin = document.getElementById("modificaPerBtn");
const SecModPerAdmin = document.getElementById("modificaPeriodoWin");
const BtnModEsAdmin = document.getElementById("modificaEsBtn");
const SecModEsAdmin = document.getElementById("modificaEserciziWin");
const BtnAggEsAdmin = document.getElementById("aggiungiEsBtn");
const SecAggEsAdmin = document.getElementById("aggiungiEserciziWin");
const BtnRimEsAdmin = document.getElementById("rimuoviEsBtn");
const SecRimEsAdmin = document.getElementById("rimuoviEserciziWin");
const BtnElScheda = document.getElementById("eliminaSchedaBtn");
const SecElScheda = document.getElementById("eliminaSchedaWin");
var erroriForm = document.getElementsByClassName("errorForm");
if(SecSchedaAdmin && SecModPerAdmin && SecModEsAdmin && SecAggEsAdmin && SecRimEsAdmin && SecElScheda) {
    showSchedaSection(SecSchedaAdmin, BtnSchedaAdmin);
    BtnSchedaAdmin.addEventListener("click", function(){
        showSchedaSection(SecSchedaAdmin, BtnSchedaAdmin);
    })
    BtnModPerAdmin.addEventListener("click", function(){
        showSchedaSection(SecModPerAdmin, BtnModPerAdmin);
    })
    BtnModEsAdmin.addEventListener("click", function(){
        showSchedaSection(SecModEsAdmin, BtnModEsAdmin);
    })
    BtnAggEsAdmin.addEventListener("click", function(){
        showSchedaSection(SecAggEsAdmin, BtnAggEsAdmin);
    })
    BtnRimEsAdmin.addEventListener("click", function(){
        showSchedaSection(SecRimEsAdmin, BtnRimEsAdmin);
    })
    BtnElScheda.addEventListener("click", function(){
        showSchedaSection(SecElScheda, BtnElScheda);
    })

    //Form periodo
    const FormModPer = document.getElementById("formPeriodo");
    const inizio = document.getElementById("inizioS");
    const fine = document.getElementById("fineS");
    
    inizio.addEventListener("change", function(){
        if(!checkData(inizio))
            showErrorForm(inizio, "La data deve essere nel formato \"dd-mm-aaaa\"");
        else if (!checkPeriodo(inizio, fine))
            showErrorForm(fine, "La data di fine periodo deve essere la stessa o successiva a quella di inizio");
        else {
            removeError(inizio);
            removeError(fine);
        }
    })
    fine.addEventListener("input", function(){
        if(!checkData(fine))
            showErrorForm(fine, "La data deve essere nel formato \"dd-mm-aaaa\"");
        else if (!checkPeriodo(inizio, fine))
            showErrorForm(fine, "La data di fine periodo deve essere la stessa o successiva a quella di inizio");
        else removeError(fine);
    })
    FormModPer.addEventListener("submit", function(event){
        if(!checkData(inizio) || !checkData(fine) || !checkPeriodo(inizio, fine)) {
            event.preventDefault();
            alert("Compilare correttamente i campi");
            erroriForm[0].previousElementSibling.focus();
        }
    })

    //Form modifica esercizi
    const FormModEs = document.getElementById("formModificaEsercizi");
    const esercizi = document.querySelectorAll(".esercizioForm");
    const ripetizioni = document.querySelectorAll("textarea.ripetizioneForm");
    const recuperi = document.querySelectorAll("textarea.recuperoForm");
    const note = document.querySelectorAll("textarea.notaForm");

    esercizi.forEach(esercizio => {
        esercizio.addEventListener("input", function() {
            if(!checkSelect(esercizio.value))
                showErrorForm(esercizio, "Selezionare un esercizio");
            else removeError(esercizio);
        })
    })

    ripetizioni.forEach(ripetizione => {
        ripetizione.addEventListener("blur", function(){
            if(!checkRipetizioni(ripetizione.value))
                showErrorForm(ripetizione, "Per questo campo sono consentiti da 3 a 40 caratteri alfanumerici e simboli - () . ; , * ×")
            else removeError(ripetizione);
        })
    })

    recuperi.forEach(recupero => {
        recupero.addEventListener("blur", function(){
            if(!checkRipetizioni(recupero.value))
                showErrorForm(recupero, "Per questo campo sono consentiti da 3 a 40 caratteri alfanumerici e simboli - () . ; , * ×")
            else removeError(recupero);
        })
    })

    note.forEach(nota => {
        nota.addEventListener("blur", function(){
            if(!checkNoteScheda(nota.value))
                showErrorForm(nota, "Per questo campo sono consentiti al massimo 500 caratteri");
            else removeError(nota);
        })
    });

    FormModEs.addEventListener("submit", function(event){
        if (!Array.from(esercizi).every(esercizio => checkSelect(esercizio.value))||
            !Array.from(ripetizioni).every(ripetizione => checkRipetizioni(ripetizione.value)) ||
            !Array.from(recuperi).every(recupero => checkRecupero(recupero.value)) ||
            !Array.from(note).every(nota => checkNoteScheda(nota.value))) {
            event.preventDefault();
            alert("Ricontrolla tutti i campi del form");
            erroriForm[0]? erroriForm[0].previousElementSibling.focus() : esercizi[0].focus();
        }
    })


    //Form aggiungi esercizio
    const FormAggEs = document.getElementById("formAggiungiEsercizio");
    const esercizioAgg = document.getElementById("new_esercizio");
    const ripetizioniAgg = document.getElementById("new_ripetizioni");
    const recuperoAgg = document.getElementById("new_recupero");
    const noteAgg = document.getElementById("new_note");
    esercizioAgg.addEventListener("blur", function(){
        if(!checkSelect(esercizioAgg))
            showErrorForm(esercizioAgg, "Selezionare un esercizio");
        else removeError(esercizioAgg);
    })
    
    ripetizioniAgg.addEventListener("blur", function(){
        if(!checkRipetizioni(ripetizioniAgg.value))
            showErrorForm(ripetizioniAgg, "Per questo campo sono consentiti da 3 a 40 caratteri alfanumerici e simboli - () . ; , * ×");
        else removeError(ripetizioniAgg);
    })

    recuperoAgg.addEventListener("blur", function(){
        if(!checkRecupero(recuperoAgg.value))
            showErrorForm(recuperoAgg, "Per questo campo sono consentiti da 3 a 40 caratteri alfanumerici e simboli - () . ; , * ×");
        else removeError(recuperoAgg);
    })

    noteAgg.addEventListener("blur", function(){
        if(!checkNoteScheda(noteAgg.value))
            showErrorForm(noteAgg, "Per questo campo sono consentiti al massimo 500 caratteri");
        else removeError(noteAgg);
    })

    FormAggEs.addEventListener("submit", function(event){
        if(!checkSelect(esercizioAgg) || !checkRipetizioni(ripetizioniAgg.value) || !checkRecupero(recuperoAgg.value) || !checkNoteScheda(noteAgg.value)) {
            event.preventDefault();
            alert("Controlla tutti i campi");
            erroriForm[0]? erroriForm[0].previousElementSibling.focus() : esercizioAgg.focus();
        }
    })

    //Form rimuovi esercizio
    const FormRimEs = document.getElementById("rimuoviEsercizioForm");
    const esercizioRim = document.getElementById("del_esercizio");
    FormRimEs.addEventListener("submit", function(event){
        if(!checkSelect(esercizioRim)) {
            event.preventDefault();
            alert("Seleziona un esercizio");
            esercizioRim.focus();
        }
    })
}

//USER//
const SecListaSchedeUser = document.getElementById("schedeWin");
const SecDatiUser = document.getElementById("datiWin");
const BtnListaSchedeUser = document.getElementById("schedeLink");
const BtnDatiUser = document.getElementById("datiLink");
const FormOldEmail = document.getElementById("oldEmail");
const FormNewEmail = document.getElementById("newEmail");
const FormCambioEmail = document.getElementById("formModificaEmail")
if(SecListaSchedeUser && SecDatiUser) {
    window.onload = () => {
        if (window.location.href.includes('user') && localStorage.getItem('user')) {
          var section = localStorage.getItem('user');
          document.querySelector(`#${section}`).click();
        }
        else showUserSection(SecListaSchedeUser, BtnListaSchedeUser);
    }

    BtnDatiUser.addEventListener("click", function() {
    showUserSection(SecDatiUser, BtnDatiUser);
    });

    BtnListaSchedeUser.addEventListener("click", function() {
    showUserSection(SecListaSchedeUser, BtnListaSchedeUser);
    });

    FormNewEmail.addEventListener("input", function() {
        if(FormNewEmail.value == FormOldEmail.value) 
            showErrorForm(FormNewEmail, "Il nuovo indirizzo email deve essere diverso dal precedente.");
        else if(!checkEmail(FormNewEmail.value)) 
            showErrorForm(FormNewEmail, "Il nuovo indirizzo email non è in un formato valido.");
        else removeError(FormNewEmail);
    })

    FormCambioEmail.addEventListener("submit", function(event) {
        if(FormNewEmail.value == FormOldEmail.value || !checkEmail(FormNewEmail.value)) {
            event.preventDefault();
            FormNewEmail.focus();
        }
    })
}

//SCHEDA USER
const BtnSchedaUser = document.getElementById("schedaBtn");
const BtnModificaSUser = document.getElementById("modificaBtn");
const SecSchedaUser = document.getElementById("schedaWin");
const SecModificaSUser = document.getElementById("modificaWin");
const FormModificaSUser = document.getElementById("editForm");
const NoteFormSUser = document.querySelectorAll("textarea");

if(SecSchedaUser && SecModificaSUser) {
    showSchedaSection(SecSchedaUser, BtnSchedaUser);
    BtnModificaSUser.addEventListener("click", function(){
        showSchedaSection(SecModificaSUser, BtnModificaSUser);
    });
    BtnSchedaUser.addEventListener("click", function(){
        showSchedaSection(SecSchedaUser, BtnSchedaUser);
    })

    NoteFormSUser.forEach(nota => {
        nota.addEventListener("input", function(){
            if(!checkNoteScheda(nota.value))
                showErrorForm(nota, "Lunghezza massima consentita: 500 caratteri");
            else removeError(nota);
        })
    });

    FormModificaSUser.addEventListener("submit", function(event){
        if(!Array.from(NoteFormSUser).every(nota => checkNoteScheda(nota.value))) {
            event.preventDefault();
            alert("Alcuni campi note non sono compilati correttamente");
            var erroriForm = document.getElementsByClassName("errorForm");
            (erroriForm[0]).previousElementSibling.focus();
        }
    })
}




/*************************************************************************************************** 
 * FUNZIONI
***************************************************************************************************/
/////////////////////
// UTILS FORM ///////
/////////////////////

//Rimuove eventuali span di errore inserite nel form
function removeError(element) {
    var errorElement = document.getElementById(element.id + "-Error");
    element.removeAttribute("aria-invalid");
    element.removeAttribute("aria-describedby");
    if (errorElement) {
        errorElement.remove()
    }
}

//Inserisce un messaggio di errore collegato ad un input nel form
function showErrorForm(element, message) {
    // Rimuove l'eventuale elemento di errore esistente
    removeError(element);
    // Crea un nuovo elemento di errore
    var newErrorElement = document.createElement("span");
    newErrorElement.id = element.id + "-Error";
    newErrorElement.className = "errorForm";
    newErrorElement.textContent = message;
    element.setAttribute("aria-describedby", newErrorElement.id);
    element.setAttribute("aria-invalid", "true");
    element.parentNode.insertBefore(newErrorElement, element.nextSibling);
}

/////////////////////
// CONTROLLO INPUT //
/////////////////////

function checkSelect(option) {
    return option.value == "" ? false : true;
}

// LOGIN //
function checkUsername(username) {
    var regExpr = /^[a-zA-Z]{4,20}$/;
    return regExpr.test(username);
}
// SIGNUP //

function checkName(name) {
    var regExpr = /^[a-zA-Z]{3,63}$/;
    return regExpr.test(name);
}

function checkPassword(password) {
    var regExpr = /^[^\s]{4,63}$/;
    return regExpr.test(password);
}
// EDIT DATI USER //
function checkEmail(email) {
    var regExp = /^([\w\-\+\.]+){2,}\@([\w\-\+\.]+){2,}\.([\w\-\+\.]+){2,4}$/;
    return regExp.test(email)
}

// SCHEDA //
// - Data
function checkData(data) {
    regExp = /^\d{4}-\d{2}-\d{2}$/;
    return (regExp.test(data.value));
}
// - Periodo
function checkPeriodo(dinizio, dfine) {
    var inizio = new Date(dinizio.value);
    var fine = new Date (dfine.value);
    return inizio <= fine;
}

// - Ripetizioni
function checkRipetizioni(ripetizioni) {
    var regExp = /^[-().;,a-zA-Z*×/\d\s]{3,40}$/;
    return regExp.test(ripetizioni)
}

// - Recupero
function checkRecupero(recupero) {
    var regExp = /^[-().;,a-zA-Z*×/\d\s]{3,40}$/;
    return regExp.test(recupero)
}

// - Note
function checkNoteScheda(note) {
    var regExp = /^[\w\d\s\S]{0,500}$/;
    return regExp.test(note);
}

/////////////////////
// AREA RISERVATA //
/////////////////////

function showUserSection(section, button) {
    // Nasconde tutte le sezioni e rende i bottoni non attivi
    var sections = document.getElementsByTagName('section');
    Array.from(sections).forEach(section => {
        section.classList.remove('active');
    });
    var buttons = document.getElementsByTagName('button');
    Array.from(buttons).forEach(button => {
        button.classList.remove('active');
    });
  
    // Mostra solo la sezione corrente e rende attivo il relativo bottone
    section.classList.add('active');
    button.classList.add('active');
    console.log("Stai visualizzando la sezione con id: " + section.id)
    
    //salva l'ultima sezione visualizzate dell'area riservata
    localStorage.setItem('user', button.id);
}

function showAdminSection(section, button) {
    var sections = document.getElementsByTagName('section');
    Array.from(sections).forEach(section => {
        section.classList.remove('active');
    });
    var buttons = document.getElementsByTagName('button');
    Array.from(buttons).forEach(button => {
        button.classList.remove('active');
    });
    section.classList.add('active');
    button.classList.add('active');
    console.log("Stai visualizzando la sezione con id: " + section.id)

    localStorage.setItem('admin', button.id);
}

function showSchedaSection(section, button) {
    var sections = document.getElementsByTagName('section');
    Array.from(sections).forEach(section => {
        section.classList.remove('active');
    });
    var buttons = document.getElementsByTagName('button');
    Array.from(buttons).forEach(button => {
        button.classList.remove('active');
    });
    section.classList.add('active');
    button.classList.add('active');
    console.log("Stai visualizzando la sezione della scheda con id: " + section.id)
}

