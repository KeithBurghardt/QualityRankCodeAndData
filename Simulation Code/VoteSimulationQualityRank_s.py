import numpy as np
import pandas as pd

class answer:
    def __init__(self,s,pos,p,r):
        
        # bias: p if position = 0, else 1-p if position = 1
        # popularity: number of votes per answer
        # time: time of each vote
        # ranking: either popularity, or recent popularity: last K votes
        self.quality = s
        self.p_rank = p
        self.p_random = r

        self.position = pos
        self.prob_vote = s#self.calc_prob_vote()
        
    def calc_prob_vote(self):
        # n.b.: we need to recalculate s before we do this
        # calc s for answer0,
        # answer1.quality = 1-s
        

        if self.position == 0:
            self.prob_vote = self.prob_vote_top()
        else:
            self.prob_vote = self.prob_vote_bottom()
    def prob_vote_top(self):
        s = self.quality
        p = self.p_rank
        r = self.p_random
        #print([p,r])

        prob = r/2 + (1-r)*(p + (1 - p)*s)
        return prob
    def prob_vote_bottom(self):
        s = self.quality
        p = self.p_rank
        r = self.p_random
        #print([p,r])
        prob = r/2 + (1-r)*((1 - p)*s)
        return prob
    
def find_position(answer0,answer1,vote_array,num_recent_votes):
    
    votes = vote_array[-num_recent_votes:]
    answer0_votes = len(votes)-np.sum(votes)
    answer1_votes = np.sum(votes)
    if answer0_votes > answer1_votes:
        answer0.position = 0
        answer1.position = 1
    elif answer1_votes > answer0_votes:
        answer0.position = 1
        answer1.position = 0
    else: #same votes
        rand_pos = np.random.binomial(1,0.5)
        answer0.position = rand_pos
        answer1.position = 1-rand_pos

def find_quality_position(answer0,answer1,dQ):
    # dQ = Q_0^2 - Q_1^2
    # if dQ >= 0, answer0 is higher quality, answer0 is ranked first
    # if dQ < 0, answer0 is lowerquality, answer0 is ranked last
    if dQ >= 0:
        answer0.position = 0
        answer1.position = 1
    else:
        answer0.position = 1
        answer1.position = 0
def find_s(Nt,nt,Nb,nb,p):
    # long complicated formula to derive dQ from votes
    # variables:
    #   - N_t: total votes when Q1 is on top
    #   - n_t: total votes for Q1 when Q1 is on top
    #   - N_b: total votes with Q1 is on bottom
    #   - n_b: total votes for Q1 when Q1 is on bottom
    #   - theta: bias
    s = 0
    if nb + nt > 0:
        #s=np.real(1/(12*(Nb+Nt))*(-((4*(nb+Nb+nt+Nt-Nb*p-2*Nt*p))/(-1+p))+(2*1j*2**(1/3)*(1j+np.sqrt(3))*(nb**2+nt**2-nt*Nt+Nt**2+2*nt*Nt*p-Nt**2*p+Nt**2*p**2+Nb*nt*(-1+4*p)-nb*(Nb-2*nt+Nt+2*Nb*p+4*Nt*p)+Nb*Nt*(2+p**2)+Nb**2*(1+p+p**2)))/(2*Nb**3-3*Nb**2*nt-3*Nb*nt**2+2*nt**3+6*Nb**2*Nt-6*Nb*nt*Nt-3*nt**2*Nt+6*Nb*Nt**2-3*nt*Nt**2+2*Nt**3-2*nb**3*(-1+p)**3-3*Nb**3*p+6*Nb**2*nt*p+21*Nb*nt**2*p-6*nt**3*p-15*Nb**2*Nt*p+9*Nb*nt*Nt*p+15*nt**2*Nt*p-21*Nb*Nt**2*p+3*nt*Nt**2*p-9*Nt**3*p-6*Nb**3*p**2+15*Nb**2*nt*p**2-45*Nb*nt**2*p**2+6*nt**3*p**2-6*Nb**2*Nt*p**2+24*Nb*nt*Nt*p**2-27*nt**2*Nt*p**2+12*Nb*Nt**2*p**2+15*nt*Nt**2*p**2+12*Nt**3*p**2+14*Nb**3*p**3-51*Nb**2*nt*p**3+39*Nb*nt**2*p**3-2*nt**3*p**3+45*Nb**2*Nt*p**3-66*Nb*nt*Nt*p**3+21*nt**2*Nt*p**3+33*Nb*Nt**2*p**3-33*nt*Nt**2*p**3-6*Nb**3*p**4+48*Nb**2*nt*p**4-12*Nb*nt**2*p**4-39*Nb**2*Nt*p**4+54*Nb*nt*Nt*p**4-6*nt**2*Nt*p**4-51*Nb*Nt**2*p**4+24*nt*Nt**2*p**4-12*Nt**3*p**4-3*Nb**3*p**5-15*Nb**2*nt*p**5+6*Nb**2*Nt*p**5-15*Nb*nt*Nt*p**5+24*Nb*Nt**2*p**5-6*nt*Nt**2*p**5+9*Nt**3*p**5+2*Nb**3*p**6+3*Nb**2*Nt*p**6-3*Nb*Nt**2*p**6-2*Nt**3*p**6+3*nb**2*(-1+p)**3*(Nb-2*nt+Nt+2*Nb*p+4*Nt*p)-3*nb*(-1+p)**3*(2*nt**2+2*Nb*nt*(-1+p)-2*nt*Nt*(1+p)+Nb**2*(-1+2*p+2*p**2)+Nt**2*(-1+p+5*p**2)+Nb*Nt*(-2+3*p+5*p**2))+3*np.sqrt(3)*np.sqrt(0j+-(Nb+Nt)**2*(-1+p)**6*(nb**4+(nt+p*(Nb+Nt+Nb*p))**2*(Nb**2+(nt+Nt*(-1+p))**2-2*Nb*(nt+Nt*(-1+p)-2*nt*p))-2*nb**3*(Nb+Nt+2*Nb*p+3*Nt*p-Nt*p**2+2*nt*(-1+p**2))+nb**2*(nt**2*(6-8*p**2)+Nb**2*(1+6*p+6*p**2)+2*nt*Nt*(-3-4*p+4*p**2+10*p**3)+Nt**2*(1+4*p+5*p**2-10*p**3+p**4)+2*Nb*(Nt*(1+5*p+5*p**2-3*p**3)+nt*(-3-p+3*p**2+6*p**3)))-2*nb*(2*nt**3*(-1+p**2)+Nb**3*p*(1+3*p+2*p**2)+nt**2*Nt*(3-p-3*p**2+6*p**3)+Nt**3*p*(1-3*p**3+2*p**4)+nt*Nt**2*(-1+p+p**2-6*p**3+6*p**4)+Nb**2*(Nt*p*(3+6*p+p**2-3*p**3)+nt*(-1-p+p**2+6*p**3+6*p**4))+Nb*(nt**2*(3-4*p-4*p**2+10*p**3)+Nt**2*p*(3+3*p-p**2-5*p**3+p**4)+nt*Nt*(-2+3*p**2+11*p**4))))))**(1/3)-1/(-1+p)**2*1j*2**(2/3)*(-1j+np.sqrt(3))*(2*Nb**3-3*Nb**2*nt-3*Nb*nt**2+2*nt**3+6*Nb**2*Nt-6*Nb*nt*Nt-3*nt**2*Nt+6*Nb*Nt**2-3*nt*Nt**2+2*Nt**3-2*nb**3*(-1+p)**3-3*Nb**3*p+6*Nb**2*nt*p+21*Nb*nt**2*p-6*nt**3*p-15*Nb**2*Nt*p+9*Nb*nt*Nt*p+15*nt**2*Nt*p-21*Nb*Nt**2*p+3*nt*Nt**2*p-9*Nt**3*p-6*Nb**3*p**2+15*Nb**2*nt*p**2-45*Nb*nt**2*p**2+6*nt**3*p**2-6*Nb**2*Nt*p**2+24*Nb*nt*Nt*p**2-27*nt**2*Nt*p**2+12*Nb*Nt**2*p**2+15*nt*Nt**2*p**2+12*Nt**3*p**2+14*Nb**3*p**3-51*Nb**2*nt*p**3+39*Nb*nt**2*p**3-2*nt**3*p**3+45*Nb**2*Nt*p**3-66*Nb*nt*Nt*p**3+21*nt**2*Nt*p**3+33*Nb*Nt**2*p**3-33*nt*Nt**2*p**3-6*Nb**3*p**4+48*Nb**2*nt*p**4-12*Nb*nt**2*p**4-39*Nb**2*Nt*p**4+54*Nb*nt*Nt*p**4-6*nt**2*Nt*p**4-51*Nb*Nt**2*p**4+24*nt*Nt**2*p**4-12*Nt**3*p**4-3*Nb**3*p**5-15*Nb**2*nt*p**5+6*Nb**2*Nt*p**5-15*Nb*nt*Nt*p**5+24*Nb*Nt**2*p**5-6*nt*Nt**2*p**5+9*Nt**3*p**5+2*Nb**3*p**6+3*Nb**2*Nt*p**6-3*Nb*Nt**2*p**6-2*Nt**3*p**6+3*nb**2*(-1+p)**3*(Nb-2*nt+Nt+2*Nb*p+4*Nt*p)-3*nb*(-1+p)**3*(2*nt**2+2*Nb*nt*(-1+p)-2*nt*Nt*(1+p)+Nb**2*(-1+2*p+2*p**2)+Nt**2*(-1+p+5*p**2)+Nb*Nt*(-2+3*p+5*p**2))+3*np.sqrt(3)*np.sqrt(0j+-(Nb+Nt)**2*(-1+p)**6*(nb**4+(nt+p*(Nb+Nt+Nb*p))**2*(Nb**2+(nt+Nt*(-1+p))**2-2*Nb*(nt+Nt*(-1+p)-2*nt*p))-2*nb**3*(Nb+Nt+2*Nb*p+3*Nt*p-Nt*p**2+2*nt*(-1+p**2))+nb**2*(nt**2*(6-8*p**2)+Nb**2*(1+6*p+6*p**2)+2*nt*Nt*(-3-4*p+4*p**2+10*p**3)+Nt**2*(1+4*p+5*p**2-10*p**3+p**4)+2*Nb*(Nt*(1+5*p+5*p**2-3*p**3)+nt*(-3-p+3*p**2+6*p**3)))-2*nb*(2*nt**3*(-1+p**2)+Nb**3*p*(1+3*p+2*p**2)+nt**2*Nt*(3-p-3*p**2+6*p**3)+Nt**3*p*(1-3*p**3+2*p**4)+nt*Nt**2*(-1+p+p**2-6*p**3+6*p**4)+Nb**2*(Nt*p*(3+6*p+p**2-3*p**3)+nt*(-1-p+p**2+6*p**3+6*p**4))+Nb*(nt**2*(3-4*p-4*p**2+10*p**3)+Nt**2*p*(3+3*p-p**2-5*p**3+p**4)+nt*Nt*(-2+3*p**2+11*p**4))))))**(1/3)));
        s=np.real(1/(12*(Nt+Nb))*(-((4*(nt+Nt+nb+Nb-Nt*p-2*Nb*p))/(-1+p))+(2*1j*2**(1/3)*(1j+np.sqrt(3))*(nt**2+nb**2-nb*Nb+Nb**2+2*nb*Nb*p-Nb**2*p+Nb**2*p**2+Nt*nb*(-1+4*p)-nt*(Nt-2*nb+Nb+2*Nt*p+4*Nb*p)+Nt*Nb*(2+p**2)+Nt**2*(1+p+p**2)))/(2*Nt**3-3*Nt**2*nb-3*Nt*nb**2+2*nb**3+6*Nt**2*Nb-6*Nt*nb*Nb-3*nb**2*Nb+6*Nt*Nb**2-3*nb*Nb**2+2*Nb**3-2*nt**3*(-1+p)**3-3*Nt**3*p+6*Nt**2*nb*p+21*Nt*nb**2*p-6*nb**3*p-15*Nt**2*Nb*p+9*Nt*nb*Nb*p+15*nb**2*Nb*p-21*Nt*Nb**2*p+3*nb*Nb**2*p-9*Nb**3*p-6*Nt**3*p**2+15*Nt**2*nb*p**2-45*Nt*nb**2*p**2+6*nb**3*p**2-6*Nt**2*Nb*p**2+24*Nt*nb*Nb*p**2-27*nb**2*Nb*p**2+12*Nt*Nb**2*p**2+15*nb*Nb**2*p**2+12*Nb**3*p**2+14*Nt**3*p**3-51*Nt**2*nb*p**3+39*Nt*nb**2*p**3-2*nb**3*p**3+45*Nt**2*Nb*p**3-66*Nt*nb*Nb*p**3+21*nb**2*Nb*p**3+33*Nt*Nb**2*p**3-33*nb*Nb**2*p**3-6*Nt**3*p**4+48*Nt**2*nb*p**4-12*Nt*nb**2*p**4-39*Nt**2*Nb*p**4+54*Nt*nb*Nb*p**4-6*nb**2*Nb*p**4-51*Nt*Nb**2*p**4+24*nb*Nb**2*p**4-12*Nb**3*p**4-3*Nt**3*p**5-15*Nt**2*nb*p**5+6*Nt**2*Nb*p**5-15*Nt*nb*Nb*p**5+24*Nt*Nb**2*p**5-6*nb*Nb**2*p**5+9*Nb**3*p**5+2*Nt**3*p**6+3*Nt**2*Nb*p**6-3*Nt*Nb**2*p**6-2*Nb**3*p**6+3*nt**2*(-1+p)**3*(Nt-2*nb+Nb+2*Nt*p+4*Nb*p)-3*nt*(-1+p)**3*(2*nb**2+2*Nt*nb*(-1+p)-2*nb*Nb*(1+p)+Nt**2*(-1+2*p+2*p**2)+Nb**2*(-1+p+5*p**2)+Nt*Nb*(-2+3*p+5*p**2))+3*np.sqrt(3)*np.sqrt(0j+-(Nt+Nb)**2*(-1+p)**6*(nt**4+(nb+p*(Nt+Nb+Nt*p))**2*(Nt**2+(nb+Nb*(-1+p))**2-2*Nt*(nb+Nb*(-1+p)-2*nb*p))-2*nt**3*(Nt+Nb+2*Nt*p+3*Nb*p-Nb*p**2+2*nb*(-1+p**2))+nt**2*(nb**2*(6-8*p**2)+Nt**2*(1+6*p+6*p**2)+2*nb*Nb*(-3-4*p+4*p**2+10*p**3)+Nb**2*(1+4*p+5*p**2-10*p**3+p**4)+2*Nt*(Nb*(1+5*p+5*p**2-3*p**3)+nb*(-3-p+3*p**2+6*p**3)))-2*nt*(2*nb**3*(-1+p**2)+Nt**3*p*(1+3*p+2*p**2)+nb**2*Nb*(3-p-3*p**2+6*p**3)+Nb**3*p*(1-3*p**3+2*p**4)+nb*Nb**2*(-1+p+p**2-6*p**3+6*p**4)+Nt**2*(Nb*p*(3+6*p+p**2-3*p**3)+nb*(-1-p+p**2+6*p**3+6*p**4))+Nt*(nb**2*(3-4*p-4*p**2+10*p**3)+Nb**2*p*(3+3*p-p**2-5*p**3+p**4)+nb*Nb*(-2+3*p**2+11*p**4))))))**(1/3)-1/(-1+p)**2*1j*2**(2/3)*(-1j+np.sqrt(3))*(2*Nt**3-3*Nt**2*nb-3*Nt*nb**2+2*nb**3+6*Nt**2*Nb-6*Nt*nb*Nb-3*nb**2*Nb+6*Nt*Nb**2-3*nb*Nb**2+2*Nb**3-2*nt**3*(-1+p)**3-3*Nt**3*p+6*Nt**2*nb*p+21*Nt*nb**2*p-6*nb**3*p-15*Nt**2*Nb*p+9*Nt*nb*Nb*p+15*nb**2*Nb*p-21*Nt*Nb**2*p+3*nb*Nb**2*p-9*Nb**3*p-6*Nt**3*p**2+15*Nt**2*nb*p**2-45*Nt*nb**2*p**2+6*nb**3*p**2-6*Nt**2*Nb*p**2+24*Nt*nb*Nb*p**2-27*nb**2*Nb*p**2+12*Nt*Nb**2*p**2+15*nb*Nb**2*p**2+12*Nb**3*p**2+14*Nt**3*p**3-51*Nt**2*nb*p**3+39*Nt*nb**2*p**3-2*nb**3*p**3+45*Nt**2*Nb*p**3-66*Nt*nb*Nb*p**3+21*nb**2*Nb*p**3+33*Nt*Nb**2*p**3-33*nb*Nb**2*p**3-6*Nt**3*p**4+48*Nt**2*nb*p**4-12*Nt*nb**2*p**4-39*Nt**2*Nb*p**4+54*Nt*nb*Nb*p**4-6*nb**2*Nb*p**4-51*Nt*Nb**2*p**4+24*nb*Nb**2*p**4-12*Nb**3*p**4-3*Nt**3*p**5-15*Nt**2*nb*p**5+6*Nt**2*Nb*p**5-15*Nt*nb*Nb*p**5+24*Nt*Nb**2*p**5-6*nb*Nb**2*p**5+9*Nb**3*p**5+2*Nt**3*p**6+3*Nt**2*Nb*p**6-3*Nt*Nb**2*p**6-2*Nb**3*p**6+3*nt**2*(-1+p)**3*(Nt-2*nb+Nb+2*Nt*p+4*Nb*p)-3*nt*(-1+p)**3*(2*nb**2+2*Nt*nb*(-1+p)-2*nb*Nb*(1+p)+Nt**2*(-1+2*p+2*p**2)+Nb**2*(-1+p+5*p**2)+Nt*Nb*(-2+3*p+5*p**2))+3*np.sqrt(3)*np.sqrt(0j+-(Nt+Nb)**2*(-1+p)**6*(nt**4+(nb+p*(Nt+Nb+Nt*p))**2*(Nt**2+(nb+Nb*(-1+p))**2-2*Nt*(nb+Nb*(-1+p)-2*nb*p))-2*nt**3*(Nt+Nb+2*Nt*p+3*Nb*p-Nb*p**2+2*nb*(-1+p**2))+nt**2*(nb**2*(6-8*p**2)+Nt**2*(1+6*p+6*p**2)+2*nb*Nb*(-3-4*p+4*p**2+10*p**3)+Nb**2*(1+4*p+5*p**2-10*p**3+p**4)+2*Nt*(Nb*(1+5*p+5*p**2-3*p**3)+nb*(-3-p+3*p**2+6*p**3)))-2*nt*(2*nb**3*(-1+p**2)+Nt**3*p*(1+3*p+2*p**2)+nb**2*Nb*(3-p-3*p**2+6*p**3)+Nb**3*p*(1-3*p**3+2*p**4)+nb*Nb**2*(-1+p+p**2-6*p**3+6*p**4)+Nt**2*(Nb*p*(3+6*p+p**2-3*p**3)+nb*(-1-p+p**2+6*p**3+6*p**4))+Nt*(nb**2*(3-4*p-4*p**2+10*p**3)+Nb**2*p*(3+3*p-p**2-5*p**3+p**4)+nb*Nb*(-2+3*p**2+11*p**4))))))**(1/3)));
    if s < 0:
        print("s<0!!")
        print(s)
        s = 0
    return s

def find_prob_vote(answer0,answer1):
    answer0.calc_prob_vote()
    answer1.calc_prob_vote()

def new_vote(answer0,answer1,vote_array,num_recent_votes):
    #answer position
    find_position(answer0,answer1,vote_array,num_recent_votes)
    # probability any answer chosen
    find_prob_vote(answer0,answer1)
    # probability answer 1 chosen

    p_1 = answer1.prob_vote
    
    vote = np.random.binomial(1,p_1)
    
    vote_array.append(vote)
def new_vote_quality(answer0,answer1,vote_array,dQ):
    #answer position
    #find_position(answer0,answer1,vote_array,num_recent_votes)
    find_quality_position(answer0,answer1,dQ)
    # probability any answer chosen
    find_prob_vote(answer0,answer1)
 
    # probability answer 1 chosen
    p_0 = answer0.prob_vote
    p_1 = answer1.prob_vote
    vote = np.random.binomial(1,p_0)    
    
    vote_array.append(vote)
    
def start_sim(s,p,r):
    answer0 = answer(s,0,p,r)
    answer1 = answer(1-s,1,p,r)
    return [answer0,answer1]

def run_sim(s,top_answer_array,vote_array,num_recent_votes,run_time,p,r,threshold):
    answer0,answer1 = start_sim(s,p,r)
    record_time = True
    time_to_top = -1
    for t in range(run_time):
        new_vote(answer0,answer1,vote_array,num_recent_votes)
        # which answer (answer0 or answer1) was the top answer
        top_answer = [answer0.position,answer1.position].index(0)
        top_answer_array.append(top_answer)
        # total votes minus votes
        votes1=np.sum(vote_array);
        votes0 = len(vote_array)-np.sum(vote_array)
        delta_vote = np.abs(votes1-votes0)
        if delta_vote > threshold and record_time:
            time_to_top = t
            record_time = False
    return time_to_top

def run_sim_quality(s,top_answer_array,vote_array,run_time,guess_p,true_p,rand,threshold):
    dQ = 0.0
    num_initial_votes = len(vote_array)
    record_time = True
    time_to_top = -1
    answer0,answer1 = start_sim(s,true_p,rand)

    for t in range(run_time):
        new_vote_quality(answer0,answer1,vote_array,dQ)
        # which answer (answer0 or answer1) was the top answer
        # 0: answer0 is top answer
        # 1: answer1 is top answer
        top_answer = answer0.position#,answer1.position].index(0)
        top_answer_array.append(top_answer)


        # 1: find all votes where answer0 is on top:
        Nt = len(vote_array[num_initial_votes:]) - np.sum(top_answer_array)
        # 2: find all votes for answer0 where answer0 is on top:
        nt = np.sum([v for v,t in zip(vote_array[num_initial_votes:],top_answer_array) if t == 0])
        # 3: find all votes where answer0 is on bottom
        Nb = np.sum(top_answer_array)#len(vote_array[num_initial_votes:]) - Nt
        # 4: find all votes for answer0 where answer0 is on bottom
        nb = np.sum([v for v,t in zip(vote_array[num_initial_votes:],top_answer_array) if t == 1])
        inferred_s = find_s(Nt,nt,Nb,nb,guess_p)

        dQ = (inferred_s-0.5)*2 # < 0: lower quality, > 0, higher quality
        

        # total votes minus votes
        votes1 = np.sum(vote_array);
        votes0 = len(vote_array)-np.sum(vote_array)
        delta_vote = np.abs(votes1-votes0)
        
        
        if delta_vote > threshold and record_time:
            time_to_top = t
            
            record_time = False
    
    return time_to_top

def export(dict_data,file):
    export_df = pd.DataFrame(data=dict_data)
    export_df.to_csv(file,index=False)
    
def run_and_export(s,vote_array,dict_data,num_recent_votes,run_time,p,r,threshold,file):
    
    top_answer_array = []
    time_to_top = run_sim(s,top_answer_array,vote_array,num_recent_votes,run_time,p,r,threshold)
    init_votes = len(vote_array)-len(top_answer_array)
    #dict_data = {'top_answer_over_time':[None]*init_votes+top_answer_array,'votes_over_time':vote_array,'num_recent_votes':[num_recent_votes]+[None]*(len(vote_array)-1),'run_time':[run_time]+[None]*(len(vote_array)-1),'Qs':qs+[None]*(len(vote_array)-2),'time_to_delta_'+str(threshold):[time_to_top]+[None]*(len(vote_array)-1)}
    dict_data['top_answer']+=top_answer_array[-1:]
    dict_data['num_recent_votes']+=[num_recent_votes]
    dict_data['run_time']+=[run_time]
    dict_data['Qs']+=[s]
    dict_data['time_to_delta_'+str(threshold)]+=[time_to_top]
                                                 

def run_and_export_voterank(s,vote_array,dict_data,run_time,p,r,threshold):
    top_answer_array = []
    # popularity is *all* votes
    num_recent_votes = run_time + len(vote_array)
    
    time_to_top = run_sim(s,top_answer_array,vote_array,num_recent_votes,run_time,p,r,threshold)
    init_votes = len(vote_array)-len(top_answer_array)
    #dict_data = {'top_answer_over_time':[None]*init_votes+top_answer_array,'votes_over_time':vote_array,'num_recent_votes':[num_recent_votes]+[None]*(len(vote_array)-1),'run_time':[run_time]+[None]*(len(vote_array)-1),'Qs':qs+[None]*(len(vote_array)-2),'time_to_delta_'+str(threshold):[time_to_top]+[None]*(len(vote_array)-1)}
    dict_data['top_answer']+=top_answer_array[-1:]
    dict_data['num_recent_votes']+=[num_recent_votes]
    dict_data['run_time']+=[run_time]
    dict_data['Qs']+=[s]
    dict_data['time_to_delta_'+str(threshold)]+=[time_to_top]

    
def run_and_export_qualityrank(s,vote_array,dict_data,run_time,guess_p,true_p,rand,threshold):
    top_answer_array = []
    time_to_top = run_sim_quality(s,top_answer_array,vote_array,run_time,guess_p,true_p,rand,threshold)
    
    init_votes = len(vote_array)-len(top_answer_array)
    #dict_data = {'top_answer_over_time':[None]*init_votes+top_answer_array,'votes_over_time':vote_array,'num_recent_votes':[num_recent_votes]+[None]*(len(vote_array)-1),'run_time':[run_time]+[None]*(len(vote_array)-1),'Qs':qs+[None]*(len(vote_array)-2),'time_to_delta_'+str(threshold):[time_to_top]+[None]*(len(vote_array)-1)}
    dict_data['top_answer']+=top_answer_array[-1:]
    dict_data['run_time']+=[run_time]
    dict_data['Qs']+=[s]
    dict_data['time_to_delta_'+str(threshold)]+=[time_to_top]


def main():
    #number of trials
    num_trials = 50
    # number of votes
    run_time = 20000
    # best fit mult in data
    # guess: 
    guess_p=0.2
    # actual:
    rand = 0.0
    rng = np.arange(0.5,0.70,0.01)
    threshold = 15

    run_quality = True

    #for threshold in [15]:#,20,30]:#10,20,30]:
    for init_votes in [200]:
        for true_p in [0.1]:#[0.1,0.2,0.3]:
            for s in rng:
                #print(s)
                for bias in [1.0]:#[0.0,0.5,1.0]:
                    print([s,run_time,bias,init_votes])

                    file_vote = 'new_sim_data/popularity_rank_vote_sim_'+'guess_p='+str(guess_p)+'true_p='+str(true_p)+'r='+str(rand)+'s='+str(round(s,4))+'_num_recent_votes=all_bias='+str(round(bias,2))+'_threshold='+str(threshold)+'_t='+str(run_time)+'_init_votes='+str(init_votes)+'_1.csv'
                    file_quality = 'new_sim_data/quality_rank_vote_sim_'+'guess_p='+str(guess_p)+'true_p='+str(true_p)+'r='+str(rand)+'s='+str(round(s,4))+'_num_recent_votes=all_bias='+str(round(bias,2))+'_threshold='+str(threshold)+'_t='+str(run_time)+'_init_votes='+str(init_votes)+'_3.csv'

                    dict_data_vote = {'top_answer':[],'num_recent_votes':[],'run_time':[],'Qs':[],'time_to_delta_'+str(threshold):[]}
                    dict_data_quality = {'top_answer':[],'run_time':[],'Qs':[],'time_to_delta_'+str(threshold):[]}
                
                    for trial in range(num_trials):
                        if trial % 100 == 0:
                            print(trial)
                    
                        init_1 = int(bias*init_votes)
                        init_0 = init_votes - init_1
                        popularity_vote_array =list(np.random.permutation(init_0*[0] + init_1*[1]))#list(np.random.binomial(1,bias,size=init_votes))
                        quality_vote_array =list(np.random.permutation(init_0*[0] + init_1*[1]))#list(np.random.binomial(1,bias,size=init_votes))
                    
 
                        #run_and_export_voterank(s,popularity_vote_array,dict_data_vote,run_time,true_p,rand,threshold)
                        if run_quality:
                            run_and_export_qualityrank(s,quality_vote_array,dict_data_quality,run_time,guess_p,true_p,rand,threshold)
                    #export(dict_data_vote,file_vote)
                    if run_quality:
                        export(dict_data_quality,file_quality)
if __name__ == "__main__":
    main()
 
    
    
