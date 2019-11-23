This is code used to parse the raw data (batch files from Mechanical Turk and SQL data). 

1. First raw SQL data is converted into a .csv files with ParseSQLData.py
2. Then we parse data by condition: guesses (CleanOpenEnded.py), unranked data (CleanUnrankedChoice.py), or ranked data (a minor modification of CleanUnrankedChoice.py).

TurkStuff.py is a set of functions and file names for (2).
