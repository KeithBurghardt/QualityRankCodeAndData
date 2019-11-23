# clean SQL data:

file = "rawNoRankData-09-05-2019.csv";#"rawGuessData-08-31-2019.csv"#"MTurkNullQData_03-13-2019.csv"#"MTurkNullQData_02-14-2019.csv"
r = open(file,"r");
w= open(file[:-4]+"_cleaned.csv","w");
for line in r:
    
    clean_line = line.replace("|",",").replace("NULL","").replace(" ","")
    w.write(clean_line[1:])
w.close()
r.close()
