function createRequestObject() {
    var ro;
    var browser = navigator.appName;
    if(browser == "Microsoft Internet Explorer"){
        ro = new ActiveXObject("Microsoft.XMLHTTP");
    }else{
        ro = new XMLHttpRequest();
    }
    return ro;
}

function handleResponse(response) {

	if(response.indexOf('|' != -1))
		parts = response.split('|');

	var db_name = parts[0];
	var db_id = parts[1];
	var total_results = parts[2];
	//alert("db: " + db_name);
	//	//alert("results: " + total_results);
	//		var finished = 1;
	//			for(i=0;i<database_ids.length;i++)
	//				{
	//						//alert("db: [" + database_ids[i] + "] [" + db_id + "]");
	//								if(database_ids[i]==db_id)
	//										{
	//													//alert("match");
	//																results_arr[i] = total_results;
	//																		}
	//																				else if(results_arr[i]=="")
	//																						{
	//																									//alert("not finished");
	//																												finished = 0;
	//																														}
	//																															}
	//																																if(finished==1)
	//																																	{
	//																																			//alert("finished");
	//																																					//clearInterval(intervalID);
	//																																							//alert("RESULTS: " + results_arr);
	//																																								}
	//																																								}
	//
	//
	//																																								function updateDisplay()
	//																																								{
	//																																									var finished = 1;
	//																																										for(i=0;i<results_arr.length;i++)
	//																																											{
	//																																													if(results_arr[i]=="")
	//																																															{
	//																																																		finished = 0;
	//																																																					break;
	//																																																							}
	//																																																								}
	//																																																									if(finished == 1)
	//																																																										{
	//																																																												//alert("DONE ---- " + results_arr);
	//																																																														clearInterval(intervalID);
	//																																																																progressDisplay();
	//																																																																		document.location.href='results.php?search_id='+searchID;
	//																																																																			}
	//																																																																				else
	//																																																																					{
	//																																																																							//alert("still working ----" + results_arr);
	//																																																																									progressDisplay();
	//																																																																											process_time++;
	//																																																																													if(process_time >= timeout)
	//																																																																															{
	//																																																																																		// vendors are taking too long to respond and may be down, cutoff search and go to results
	//																																																																																					goToResults();
	//																																																																																							}
	//																																																																																								}
	//																																																																																									//alert(database_ids + " | " + results_arr);
	//																																																																																									}
	//
	//																																																																																									function progressDisplay()
	//																																																																																									{
	//																																																																																										var divTag = document.getElementById('searchStatusWindow');
	//																																																																																											var res = "<table width=600 style='border:1px solid #000000;'><tr><td bgcolor='#999999' style='font-size:22pt; font-weight:bold; color:#FFFFFF'>Academic Web Database</td><td bgcolor='#999999' style='font-size:22pt; font-weight:bold; color:#FFFFFF'>Results</td></tr>\n";
	//																																																																																												//var res = "<table width=600><tr><td>Academic Web Database</td><td>Status</td></tr>\n";
	//																																																																																													for(i=0;i<results_arr.length;i++)
	//																																																																																														{
	//																																																																																																var db_name = database_names[i];
	//																																																																																																		if(results_arr[i]=="")
	//																																																																																																				{
	//																																																																																																							res += "\n<tr><td>" + db_name + "</td><td width='100'><img src='img/loading_orange.gif'></td></tr>";
	//																																																																																																									}
	//																																																																																																											else
	//																																																																																																													{
	//																																																																																																																res += "\n<tr><td>" + db_name + "</td><td width='100'>" + results_arr[i] + "</td></tr>";
	//																																																																																																																		}
	//																																																																																																																			}
	//																																																																																																																				res += "</table>";
	//																																																																																																																					divTag.innerHTML = res;
	//																																																																																																																					}
