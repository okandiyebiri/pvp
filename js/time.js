window.mtInfo= {
    name: "masterTime",
    desc: "Javascript Libary for Time Management",
    vers: 0.002,
    author: "Aykut Kardaş",
    contact: "aykutkrds[et]gmail.com"
}

function masterTime(targetIndex){window.mtTargets=document.querySelectorAll("*[mt-time]"),window.mtTask=function(targetIndex){var time=parseInt(eval(mtTargets[targetIndex].getAttribute("mt-time"))),way=mtTargets[targetIndex].getAttribute("mt-way")||"down",fnFunc=mtTargets[targetIndex].getAttribute("mt-finishFunc"),localTime=localStorage.getItem("timeEgg"+targetIndex);if("down"==way)var end=parseInt(mtTargets[targetIndex].getAttribute("mt-end"))||0;else if("up"==way)var end=parseInt(mtTargets[targetIndex].getAttribute("mt-end"))+1||time+10;var hour=Math.floor(time/60/60),minute=Math.floor(time%3600/60),second=time%3600%60;10>hour&&(hour="0"+hour),10>minute&&(minute="0"+minute),10>second&&(second="0"+second),localTime-time==1||localTime-time==0?end>=time&&("up"==way?time++:(eggDestroy(targetIndex),eval(fnFunc))):time=localTime+1,localTime-time==-1||localTime-time==0?time>=end&&("down"==way?time--:(eggDestroy(targetIndex),eval(fnFunc))):time=localTime-1,mtTargets[targetIndex].innerHTML=hour+":"+minute+":"+second,mtTargets[targetIndex].setAttribute("mt-time",time),localStorage.setItem("timeEgg"+targetIndex,time)};var mtTargets=document.querySelectorAll("*[mt-time]");window.eggDestroy=function(targetEgg){window.mtTasksEnd="clearInterval(timeEggTask"+targetEgg+")",eval(mtTasksEnd)};for(var i=0;i<mtTargets.length;i++){var time=parseInt(eval(mtTargets[i].getAttribute("mt-time")));localStorage.setItem("timeEgg"+i,time);var newTask="window.timeEgg"+i+"=new mtTask("+i+");",newEggTask="window.timeEggTask"+i+"=setInterval('timeEgg"+i+".constructor("+i+");',1000);";window.mtTasks=newTask+newEggTask,eval(mtTasks)}}window.addEventListener("load",function(){masterTime()});