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

sys.path.append("/home/posfaim/projects/turk/experiments/")
from TurkStuff import *


alldata	= GetChoices(unrankedchoice_exps[:2])
	

count_high	= 0
count_low	= 0		
print "SurveyCode,Q,Opt_top,Opt_bottom,Choice"
for sc in alldata:
	for Q in range(10):
		sys.stdout.write("%s,%d,%f,%f,%d\n"%(sc, Q, alldata[sc][Q][0],alldata[sc][Q][1],alldata[sc][Q][2]))
		if alldata[sc][Q][2]==0:
			count_high+=1
		else:
			count_low+=1
p = count_high/float(count_high + count_low) 
print >>sys.stderr, p, np.sqrt(p*(1-p)/float(count_high + count_low))*1.96

#save answers separately by question
for Qid in range(10):
	with open("unrankedchoice-Q"+str(Qid)+".csv", mode='w') as fout:
		fout.write("high_rank,low_rank,choice\n")
		for sc in alldata:
			fout.write("%f,%f,%d\n"%(alldata[sc][Qid][0],alldata[sc][Qid][1],int(alldata[sc][Qid][2])))

