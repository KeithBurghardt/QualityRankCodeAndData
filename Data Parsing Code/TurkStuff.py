#!/usr/bin/python

import numpy as np
from scipy.optimize import minimize
from scipy.stats import shapiro
import scipy.stats
import matplotlib.pyplot as plt
import sys
import itertools
import csv

'''
0: "Perimeter1.png":2.91393123655
1: "Line1.png":2.11605429697
2: "Area1.png": 4.27852
3: "Area2.png": 2.24069551
4: "Text1.png" (r's): 47
5: "CountProb-fig-6.png": 476
6: "CountProb-fig-7.png" (blue): 1043
7: "CountProb-fig-8.png": 568
8: "CountProb-fig-9.png": 2069
9: "CountProb-fig-10.png" (pink): 312
'''

cans = [2.91393123655, 2.11605429697, 4.27852, 2.24069551, 47, 476, 347, 568, 2069, 312]


#batch and data files in temporal order
prefix	= "/home/posfaim/projects/turk/experiments/expfiles/"
batchfiles= [							#experiment start date		id
	"openended_Batch_3351317_batch_results.csv", 		#08-24-2018			0
	"openended_Batch_3385763_batch_results.csv",		#09-28-2018			1
	"rankedchoice_Batch_3438695_batch_results.csv",		#11-15-2018			2
	"rankedchoice_Batch_3522925_batch_results.csv",		#02-05-2019			3
	"rankedchoice_Batch_3532861_batch_results.csv",		#02-14-2019 3:20 PM PST		4
	"unrankedchoice_Batch_3532972_batch_results.csv",	#02-14-2019 5:22 PM PST		5
	"unrankedchoice_Batch_3533008_batch_results.csv",	#02-14-2019 6:14 PM PST		6
	"rankedchoice_Batch_3566482_batch_results.csv",		#03-13-2019			7
	"openended_Batch_3750792_batch_results.csv", 		#08-28-2019			8
	"openended_Batch_3751823_batch_results.csv",		#08-29-2019			9
	"openended_Batch_3752900_batch_results.csv", 		#08-30-2019			10
	"unrankedchoice_Batch_3757778_batch_results.csv",	#09-05-2019 10:08 AM PDT	11
	"unrankedchoice_Batch_3757910_batch_results.csv"	#09-05-2019 12:38 PM PDT	12
]

datafiles= [							#experiment start date
	"openended_TurkerGuess08-25-2018.csv", 			#08-24-2018
	"openended_TurkerGuess09-29-2018.csv",			#09-28-2018
	"rankedchoice_MTurkQData_11-15-2018.csv",		#11-15-2018
	"rankedchoice_MTurkQData_02-05-2019.csv",		#02-05-2019
	"rankedchoice_MTurkQData_02-14-2019.csv",		#02-14-2019 3:20 PM PST
	"unrankedchoice_MTurkNullQData_02-14-2019_cleaned.csv",	#02-14-2019 5:22 PM PST
	"unrankedchoice_MTurkNullQData_02-14-2019_cleaned.csv",	#02-14-2019 6:14 PM PST
	"rankedchoice_MTurkNullQData_03-13-2019_cleaned.csv",	#03-13-2019
	"openended_GuessData-08-31-2019_cleaned.csv", 		#08-28-2019
	"openended_GuessData-08-31-2019_cleaned.csv",		#08-29-2019
	"openended_GuessData-08-31-2019_cleaned.csv",		#08-30-2019
	"unrankedchoice_NoRankData-09-05-2019_cleaned.csv",	#09-05-2019 10:08 AM PDT
	"unrankedchoice_NoRankData-09-05-2019_cleaned.csv"	#09-05-2019 12:38 PM PDT
]

openended_exps		= [0,1,8,9,10]
unrankedchoice_exps	= [5,6,11,12]
rankedchoice_exps	= [2,3,4,7]


#get worker ids and survey codes
#but only if this is the first participation of the worker
def GetWorkerIDsSCs(expids):
	if isinstance(expids, int):
		expids = [expids]
	
	wids	= []
	scs	= []
	for exp1 in expids:
		previous_wids = []
		for exp2 in range(exp1):
			with open(prefix+batchfiles[exp2], mode='r') as csvfile:
				dict_reader = csv.DictReader(csvfile)
				for row in dict_reader:
					previous_wids.append(row["WorkerId"])		
		with open(prefix+batchfiles[exp1], mode='r') as csvfile:
				dict_reader = csv.DictReader(csvfile)
				for row in dict_reader:
					if row["WorkerId"] not in previous_wids:
						wids.append(row["WorkerId"])		
						scs.append(row["Answer.surveycode"])
	return wids, scs


####   Open-ended experiment

#get guesses and the times spent on questions
def GetGuessesTime(expids):
	if isinstance(expids, int):
		expids = [expids]
	
	guesses	= {}
	times	= {}
	orders	= {}
	
	for exp1 in expids:
		wids, scs = GetWorkerIDsSCs(exp1)
		for sc in scs:
			guesses[sc]	= np.zeros(10,dtype=np.float)
			times[sc]	= np.zeros(10,dtype=np.float)
			orders[sc]	= np.zeros(10,dtype=np.float)
			
		with open(prefix+datafiles[exp1], mode='r') as csvfile:
			dict_reader = csv.DictReader(csvfile)
			for row in dict_reader:
				if row['survey_code'] in scs:
					if row['RandQ']!='NULL' and row['RandQ']!='':
						qid	= int(row['RandQ'])-1
						sc	= row['survey_code'] 
						guesses[sc][qid] = row['Guess']
					
						#calculate time spent on question
						#csv file has time when questions are started and when complete task ends
						#this assumes that questions are in temporal order in csv
						tstart	= float(row["TimeStarted"])
						qorder	= int(row['QNum'])
						orders[sc][qid]	= qorder-1
					
						if qorder > 1:
							times[sc][qid_prev]  = tstart - tstart_prev
						tstart_prev	= tstart
						qid_prev	= qid
					elif row['end_time']!='NULL' and row['end_time']!='':
						sc	= row['survey_code']
						tend9	= float(row["end_time"])
						times[sc][qid_prev] = tend9 - tstart_prev
						qid_prev	= None
						tstart_prev	= None
	
	return guesses, times, orders


def GetGuesses(expids):
	if isinstance(expids, int):
		expids = [expids]
	
	guesses	= [ [] for qid in range(10)]
	
	for exp1 in expids:
		wids, scs = GetWorkerIDsSCs(exp1)
			
		with open(prefix+datafiles[exp1], mode='r') as csvfile:
			dict_reader = csv.DictReader(csvfile)
			for row in dict_reader:
				if row['survey_code'] in scs:
					if row['RandQ']!='NULL' and row['RandQ']!='':
						qid	= int(row['RandQ'])-1
						guesses[qid].append(float(row['Guess']))

	for qid in range(10):
		guesses[qid].sort()					
						
	return guesses

#read cleaned data
def GetCleanData(filename):
	data	= []
	with open(filename, mode='r') as csvfile:
		dict_reader = csv.DictReader(csvfile)
		for row in dict_reader:
			sc	= row['SurveyCode']
			data.append({})
			data[-1]['SurveyCode']=row['SurveyCode']
			data[-1]['Guess'] = float(row['Guess'])
			data[-1]['Time'] = float(row['Time'])
			data[-1]['Guess'] = float(row['Guess'])
			data[-1]['CorrectAnswer'] = float(row['CorrectAnswer'])
			data[-1]['Q'] = int(row['Q'])
			data[-1]['QOrder'] = int(row['QOrder'])
			data[-1]['Batch'] = int(row['Batch'])
			
	return data


####   Choice experiment

def GetChoices(expids):
	if isinstance(expids, int):
		expids = [expids]
	
	
	all_data = {}	
	for exp1 in expids:
		wids, scs = GetWorkerIDsSCs(exp1)

		data = {sc: [np.zeros(3) for i in range(10)] for sc in scs}#each entry: high ranked option, low ranked option, choice
		
		with open(prefix+datafiles[exp1], mode='r') as csvfile:
			dict_reader = csv.DictReader(csvfile)
			rawdata	= [row for row in dict_reader]
		
		for sc in scs:
			for Q in range(10):
				#select appropriate rows
				selected = []
				for row in rawdata:
					if (row["RandQ"].isdigit() and int(row["RandQ"])-1==Q) and row["survey_code"]==sc:
						selected.append(row)
				if len(selected)>1:
					for row2 in selected[:2]:
						if int(row2['AnswerOrder'])==1:
							data[sc][Q][0] = float(row2["Guess"]) #high ranked option
						if int(row2['AnswerOrder'])==2:
							data[sc][Q][1] = float(row2["Guess"]) #low ranked option
					#which one was chosen
					chosen_id = int(selected[0]['AnswerChosen'])-1
					if int(selected[chosen_id]['AnswerOrder'])==1:
						data[sc][Q][2] = 0 #high ranked was chosen
					else:
						data[sc][Q][2] = 1 #low ranked was chosen
				else:
					print >>sys.stderr, "Missing data"
		
		
		all_data.update(data)
			
	return all_data





def main():
	data = GetChoices(12)

	print data

	return

if __name__ == '__main__':
	main()
		
